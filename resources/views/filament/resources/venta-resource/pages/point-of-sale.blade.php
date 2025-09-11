<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Columna Principal: Catálogo de Productos --}}
        <div class="lg:col-span-2">
            {{-- Barra de Búsqueda y Filtros --}}
            <div class="mb-4">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model.live.debounce.300ms="searchTerm"
                                       placeholder="Buscar artículos por nombre o código..."/>
                </x-filament::input.wrapper>
            </div>

            {{-- Grid de Productos --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 h-[60vh] overflow-y-auto p-2">
                @forelse($articles as $article)
                <div wire:click="addToCart({{ $article->id }})"
                     class="cursor-pointer border rounded-lg p-4 text-center hover:bg-gray-100 dark:hover:bg-gray-700 flex flex-col justify-between">
                    <div>
                        {{-- <img src="{{ $article->imagen_url }}" alt="{{ $article->nombre }}"
                                  class="h-24 w-24 mx-auto mb-2 object-cover"> --}}
                        <h3 class="font-semibold text-sm">{{ $article->nombre }}</h3>
                    </div>
                    <p class="text-sm text-gray-500">{{ \Illuminate\Support\Number::currency($article->precio, 'COP')
                        }}</p>
                </div>
                @empty
                <p class="col-span-full text-center text-gray-500">No se encontraron artículos.</p>
                @endforelse
            </div>
        </div>

        {{-- Columna Derecha: Carrito de Compras --}}
        <div class="lg:col-span-1">
            <div class="border rounded-lg p-4 bg-white dark:bg-gray-800 flex flex-col h-full">
                <h2 class="text-lg font-bold mb-4">Ticket de Venta</h2>

                <div class="flex-grow space-y-2 overflow-y-auto">
                    @forelse($cart as $articleId => $item)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-sm">{{ $item['nombre'] }}</p>
                            <p class="text-xs text-gray-500">{{ \Illuminate\Support\Number::currency($item['precio'],
                                'COP') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="updateQuantity({{ $articleId }}, {{ $item['cantidad'] - 1 }})"
                                    class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600">-
                            </button>
                            <span>{{ $item['cantidad'] }}</span>
                            <button wire:click="updateQuantity({{ $articleId }}, {{ $item['cantidad'] + 1 }})"
                                    class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600">+
                            </button>
                            <button wire:click="removeFromCart({{ $articleId }})"
                                    class="text-red-500 hover:text-red-700 ml-2">
                                <x-heroicon-o-trash class="w-5 h-5"/>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center mt-8">El carrito está vacío.</p>
                    @endforelse
                </div>

                @if(!empty($cart))
                <div class="mt-auto pt-4 border-t">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>{{ \Illuminate\Support\Number::currency($total, 'COP') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span>{{ \Illuminate\Support\Number::currency($total, 'COP') }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        {{ $this->checkoutAction }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
