<div class="relative inline-block text-left">
    <select
        wire:model="bodega_id"
        wire:change="changeBodega($event.target.value)"
        class="
            inline-block rounded-lg border border-gray-300
            bg-white text-black
            dark:bg-gray-800 dark:text-white
            dark:border-gray-600
            shadow-sm px-13 py-1 pr-9 text-sm   <!-- pr-6 deja espacio para la flecha -->
            focus:outline-none focus:ring-2 focus:ring-primary-500
            focus:border-primary-500 transition-colors
            min-w-[4rem] max-w-xs        <!-- ancho mínimo y máximo -->
        "
    >
        @foreach($bodegas as $id => $nombre)
            <option value="{{ $id }}">{{ $nombre }}</option>
        @endforeach
    </select>
</div>
