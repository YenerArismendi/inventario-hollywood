<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Helpers\ProductHelper;
use App\Models\Brand;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Obtenemos los datos necesarios del formulario
        $nombre = $data['nombre'];
        $brandId = $data['brand_id'];
        $presentacion = $data['presentation'];
        $brandName = Brand::find($brandId)?->name;

        // Si tenemos la información necesaria, generamos el código único
        if (!empty($nombre) && !empty($brandName)) {
            $data['codigo'] = ProductHelper::generarCodigoUnico($nombre, $brandName, $presentacion);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
