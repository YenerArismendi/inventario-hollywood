<x-filament::page>
    <h2 class="text-2xl font-bold mb-4">Mapa de la Bodega: {{ $bodega->nombre ?? 'Sin datos' }}</h2>

    @if ($bodega)
        <div class="grid gap-4">
            @foreach ($bodega->estantes as $estante)
                <div>
                    <h3 class="font-semibold mb-2">{{ $estante->nombre }}</h3>
                    <div class="grid grid-cols-{{ $bodega->columnas }} gap-1">
                        @foreach ($estante->ubicaciones as $ubicacion)
                            <div
                                class="border rounded-md flex items-center justify-center h-12 cursor-pointer
                                    {{ $ubicacion->producto_id ? 'bg-green-400 hover:bg-green-500' : 'bg-gray-200 hover:bg-gray-300' }}"
                                wire:click="$emit('abrirModalProducto', {{ $ubicacion->id }})"
                                title="{{ $ubicacion->producto?->nombre ?? 'Vacío' }}">
                                {{ $ubicacion->producto?->nombre ?? '—' }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>No hay bodegas registradas.</p>
    @endif
</x-filament::page>
