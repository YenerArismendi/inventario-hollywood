@php
$ubicaciones = $estante->ubicaciones->sortBy('posicion');
$filas = $ubicaciones->groupBy(fn($u) => substr($u->posicion, 0, 1));
@endphp

<div class="p-6 space-y-3">
    @if ($filas->isEmpty())
        <div class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">
            🗃️ No hay ubicaciones registradas para este estante.
        </div>
    @else
        @foreach ($filas as $fila => $espacios)
            <div class="flex gap-3 justify-center flex-wrap">
                @foreach ($espacios as $espacio)
                    <div
                        class="w-16 h-16 border rounded-lg flex items-center justify-center text-xs font-medium cursor-pointer
                        transition-all duration-150 ease-in-out shadow-sm 
                        {{ $espacio->articles_id 
                            ? 'bg-green-400 hover:bg-green-500 text-white border-green-500' 
                            : 'bg-gray-200 hover:bg-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-700' }}"
                        wire:click="$dispatch('abrirModalUbicacion', @js(['ubicacionId' => $espacio->id]))"
                        title="{{ $espacio->producto?->nombre ?? 'Vacío' }}">
                        <span class="truncate px-1 text-center">{{ $espacio->producto?->nombre ?? $espacio->posicion }}</span>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>

{{-- ✅ Modal Livewire --}}
@livewire('editar-ubicacion-modal')
