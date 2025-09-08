<?php
//
//namespace App\Filament\Widgets;
//
//use Filament\Widgets\Widget;
//use App\Models\Article;
//
//class ProductosWidget extends Widget
//{
//    protected static string $view = 'filament.widgets.productos-widget';
//
//    // Listener para refrescar cuando cambie la bodega
//    protected $listeners = ['bodegaChanged' => '$refresh'];
//
//    public function getData(): array
//    {
//        $bodegaId = auth()->user()->active_bodega_id;
//
//        return [
//            'total' => Article::where('bodega_id', $bodegaId)->count(),
//        ];
//    }
//}
