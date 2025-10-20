<x-mail::message>
    # Alerta de Inventario Bajo

    Hola,

    Se ha detectado un nivel de stock bajo para uno de los artículos en tu bodega.

    **Artículo:** {{ $article->nombre }} ({{ $article->codigo }})
    **Bodega:** {{ $bodega->nombre }}
    **Stock Actual:** **{{ $stockActual }} unidades**

    El umbral de alerta está configurado en 100 unidades. Por favor, considera realizar un nuevo pedido o un traslado de inventario.

    <x-mail::button :url="route('filament.admin.resources.articles.index')">
        Ver Inventario
    </x-mail::button>

    Gracias,<br>
    {{ config('app.name') }}
</x-mail::message>
