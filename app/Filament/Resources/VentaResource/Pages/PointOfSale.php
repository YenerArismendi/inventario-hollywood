<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Filament\Resources\SesionCajaResource;
use App\Models\Article;
use App\Models\Venta;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class PointOfSale extends Page implements Forms\Contracts\HasForms, \Filament\Actions\Contracts\HasActions
{
    use Forms\Concerns\InteractsWithForms;
    use \Filament\Actions\Concerns\InteractsWithActions;

    protected static string $resource = VentaResource::class;

    protected static string $view = 'filament.resources.venta-resource.pages.point-of-sale';

    public ?array $cart = [];
    public float $total = 0;
    public string $searchTerm = '';
    protected ?object $sesionActiva = null;
    public ?int $bodegaId = null;

    public function mount(): void
    {
        $this->sesionActiva = auth()->user()->sesionCajaActiva;

        if (!$this->sesionActiva) {
            Notification::make()
                ->title('No hay sesión de caja abierta.')
                ->body('Debes abrir una sesión de caja antes de poder registrar ventas.')
                ->danger()
                ->send();
            $this->redirect(SesionCajaResource::getUrl('index'));
        } else {
            $this->bodegaId = $this->sesionActiva->caja->bodega_id;
        }
    }

    protected function getViewData(): array
    {
        return [
            // Hacemos que la propiedad computada 'articles' esté disponible explícitamente en la vista.
            'articles' => $this->getArticlesProperty(),
        ];
    }

    public function getArticlesProperty()
    {
        if (!$this->bodegaId) return [];

        return Article::query()
            ->whereHas('bodegas', function ($query) {
                $query->where('bodega_id', $this->bodegaId)->where('stock', '>', 0);
            })
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', "%{$this->searchTerm}%")
                        ->orWhere('codigo', 'like', "%{$this->searchTerm}%");
                });
            })
            ->limit(20)
            ->get();
    }

    public function addToCart(int $articleId): void
    {
        $article = Article::find($articleId);
        if (!$article) return;

        $stock = $article->bodegas()->where('bodega_id', $this->bodegaId)->first()?->pivot->stock ?? 0;

        if (isset($this->cart[$articleId])) {
            if ($this->cart[$articleId]['cantidad'] < $stock) {
                $this->cart[$articleId]['cantidad']++;
            } else {
                Notification::make()->title('Stock máximo alcanzado para este artículo.')->warning()->send();
            }
        } else {
            $this->cart[$articleId] = [
                'nombre' => $article->nombre,
                'precio' => $article->precio,
                'cantidad' => 1,
            ];
        }
        $this->recalculateTotal();
    }

    public function updateQuantity(int $articleId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->removeFromCart($articleId);
            return;
        }

        $article = Article::find($articleId);
        $stock = $article->bodegas()->where('bodega_id', $this->bodegaId)->first()?->pivot->stock ?? 0;

        if ($quantity > $stock) {
            $this->cart[$articleId]['cantidad'] = $stock;
            Notification::make()->title('Stock máximo alcanzado.')->warning()->send();
        } else {
            $this->cart[$articleId]['cantidad'] = $quantity;
        }
        $this->recalculateTotal();
    }

    public function removeFromCart(int $articleId): void
    {
        unset($this->cart[$articleId]);
        $this->recalculateTotal();
    }

    protected function recalculateTotal(): void
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return $item['precio'] * $item['cantidad'];
        });
    }

    public function getCheckoutActionProperty(): Action
    {
        return Action::make('checkout')
            ->label('Cobrar')
            ->color('success')
            ->disabled(empty($this->cart))
            ->form([
                // Hacemos la carga de opciones explícita para mayor robustez en una página custom.
                Forms\Components\Select::make('cliente_id')
                    ->label('Cliente (Opcional)')
                    // Envolvemos la consulta en una closure para que se ejecute en el momento correcto.
                    ->options(function () {
                        return \App\Models\Cliente::pluck('nombre', 'id');
                    })
                    ->searchable()
                    ->preload(), // Mejora de UX: muestra los clientes al hacer clic.
                Forms\Components\Select::make('metodo_pago')->options(['efectivo' => 'Efectivo', 'transferencia' => 'Transferencia', 'tarjeta' => 'Tarjeta'])->required(),
                Forms\Components\TextInput::make('descuento')->numeric()->default(0)->prefix('COP'),
            ])
            ->action(function (array $data) {
                $this->handleSale($data);
            });
    }

    protected function handleSale(array $paymentData): void
    {
        try {
            DB::transaction(function () use ($paymentData) {
                $venta = Venta::create([
                    'user_id' => auth()->id(),
                    'bodega_id' => $this->bodegaId,
                    'cliente_id' => $paymentData['cliente_id'],
                    'sesion_caja_id' => $this->sesionActiva->id,
                    'subtotal' => $this->total,
                    'descuento' => $paymentData['descuento'],
                    'total' => $this->total - $paymentData['descuento'],
                    'metodo_pago' => $paymentData['metodo_pago'],
                ]);

                foreach ($this->cart as $articleId => $item) {
                    $venta->detalles()->create(['article_id' => $articleId, 'cantidad' => $item['cantidad'], 'precio_unitario' => $item['precio'], 'subtotal_item' => $item['cantidad'] * $item['precio']]);
                    $this->sesionActiva->caja->bodega->articles()->updateExistingPivot($articleId, ['stock' => DB::raw("stock - {$item['cantidad']}")]);
                }
            });

            Notification::make()->title('¡Venta registrada con éxito!')->success()->send();
            $this->cart = [];
            $this->recalculateTotal();

        } catch (\Exception $e) {
            Notification::make()->title('Error al registrar la venta')->body($e->getMessage())->danger()->send();
        }
    }

    public function checkout(): void
    {
        // prueba rápida para verificar que el botón llegue al servidor
        // usa dd() temporalmente para ver si entra
        dd('checkout pressed', $this->cart);
    }
}
