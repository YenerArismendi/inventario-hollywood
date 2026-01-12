<div class="space-y-4">
    @if($record->bodegas->isEmpty())
    <div class="text-gray-500 dark:text-gray-400">
        No hay stock registrado en ninguna bodega.
    </div>
    @else
    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Bodega</th>
                    <th scope="col" class="px-6 py-3">Tipo</th>
                    <th scope="col" class="px-6 py-3 text-right">Cantidad</th>
                    <th scope="col" class="px-6 py-3 text-right">Costo Promedio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($record->bodegas as $bodega)
                @if($bodega->pivot->stock > 0)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $bodega->nombre }}
                    </td>
                    <td class="px-6 py-4">
                        {{ ucfirst($bodega->tipo) }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        {{ number_format($bodega->pivot->stock, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        ${{ number_format($bodega->pivot->costo_unitario_promedio, 2) }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="text-right font-bold mt-2">
        Total Global: {{ number_format($record->stock, 0, ',', '.') }} {{ $record->unidad_consumo }}
    </div>
    @endif
</div>