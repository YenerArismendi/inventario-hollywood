<x-filament::modal id="editar-ubicacion" width="md">
    <x-slot name="heading">
        ✏️ Editar ubicación
    </x-slot>

    <form wire:submit.prevent="guardar" class="space-y-5">
        {{-- Campo de selección --}}
        <div>
            <label for="producto" 
                   class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                Seleccionar producto
            </label>

            <div class="relative">
                <select
                    wire:model="articles_id"
                    id="producto"
                    class="w-full appearance-none border border-gray-300 dark:border-gray-700 text-black rounded-lg px-3 py-2.5 text-sm 
                           bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 
                           focus:ring-2 focus:ring-primary-500 focus:border-primary-500 
                           transition duration-150 ease-in-out"
                >
                    <option value="">-- Selecciona un producto --</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                    @endforeach
                </select>

                {{-- 🔽 Ícono decorativo --}}
                <svg class="absolute right-3 top-3.5 w-4 h-4 text-gray-400 pointer-events-none" 
                     xmlns="http://www.w3.org/2000/svg" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            {{-- 💡 Ayuda visual --}}
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Selecciona el producto que deseas asignar a esta ubicación.
            </p>
        </div>

        {{-- Botón de guardar --}}
        <div class="flex justify-end">
            <x-filament::button type="submit" color="success" icon="heroicon-o-check-circle">
                Guardar cambios
            </x-filament::button>
        </div>
    </form>
</x-filament::modal>
