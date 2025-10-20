{{--
Este archivo recibe una variable:
- $record: La instancia del modelo App\Models\Article
--}}
<div class="p-4 space-y-4">
    {{-- Cargamos la relación 'bodegas' para evitar N+1 queries si no viene cargada --}}
    @php
    $record->loadMissing(['bodegas','proveedor']);
    $bodegasConStock = $record->bodegas->filter(fn ($bodega) => $bodega->pivot->stock > 0);
    @endphp

    {{-- Tabla con los detalles de un articulo --}}
    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <tbody>
            {{-- Fila para el Proveedor --}}
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="py-3 px-6 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    Proveedor
                </th>
                <td class="py-3 px-6 font-semibold text-gray-900 dark:text-white">
                    {{ $record->proveedor?->name ?? 'No asignado' }}
                </td>
            </tr>

            {{-- Filas para el Stock en Bodegas --}}
            @if($bodegasConStock->isNotEmpty())
            @foreach($bodegasConStock as $bodega)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <th scope="row" class="py-3 px-6 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    Stock en <span class="text-gray-900 dark:text-white">{{ $bodega->nombre }}</span>
                </th>
                <td class="py-3 px-6 font-bold text-gray-900 dark:text-white">
                    {{ number_format($bodega->pivot->stock, 0) }}
                </td>
            </tr>
            @endforeach
            @else
            {{-- Mensaje si no hay stock en ninguna bodega --}}
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="py-3 px-6 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    Stock
                </th>
                <td class="py-3 px-6 text-yellow-600 dark:text-yellow-400">
                    Este artículo no tiene unidades registradas.
                </td>
            </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
