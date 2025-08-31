<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Filament\Widgets\ArticleVariantsStats;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Forms;
use App\Models\Variante;
use Filament\Notifications\Notification;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

//    Tarjeta para mostrar la cantidad de variables de los productos
//    protected function getHeaderWidgets(): array
//    {
//        return [
//            ArticleVariantsStats::class,
//        ];
//    }

    protected function getHeaderActions(): array
    {
        return [
            // Mostrar cuántas variantes hay (informativo)
            Actions\Action::make('variantesCount')
                ->label(fn () => 'Variantes: ' . $this->record->variantes()->count())
                ->disabled()
                ->color('gray'),

            // Botón para agregar una variante
            Actions\Action::make('crearVariante')
                ->label('Agregar variante')
                ->icon('heroicon-o-plus')
                ->modalHeading('Crear nueva variante')
                ->modalSubmitActionLabel('Guardar')
                ->form([
                    Forms\Components\TextInput::make('medida')
                        ->label('Medida')
                        ->required()
                        ->maxLength(50),

                    Forms\Components\TextInput::make('color')
                        ->label('Color')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('material')
                        ->label('Material')
                        ->maxLength(50),
                    Forms\Components\Select::make('calidad')
                        ->label('Calidad')
                        ->options([
                            'alta' => 'Alta',
                            'mediana' => 'Mediana',
                            'baja' => 'Baja',
                        ])
                ])
                ->action(function (array $data): void {
                    $this->record->variantes()->create($data);

                    Notification::make()
                        ->title('Variante creada correctamente')
                        ->success()
                        ->send();
                })
                ->modalWidth('md')
                ->color('success'),

            // Botón para ver variantes existentes en un modal (tabla simple)
            Actions\Action::make('verVariantes')
                ->label('Ver variantes')
                ->icon('heroicon-o-eye')
                ->modalHeading('Variantes de este artículo')
                ->modalContent(function () {
                    $variantes = $this->record->variantes()->get();

                    return view('filament.modals.variantes', [
                        'variantes' => $variantes,
                    ]);
                })
                ->modalWidth('lg')
                ->color('gray'), // Usa "gray" en lugar de "secondary"

            Actions\DeleteAction::make(),
        ];
    }
}
