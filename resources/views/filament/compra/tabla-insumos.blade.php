<!-- compras/form.blade.php -->

<!-- 🔹 Mantén comentado el include de alertas -->
@include('components.alertas')

<!-- ✅ Carga tu JS personalizado -->
<script src="{{ asset('js/compra-form.js') }}"></script>

<!-- Scripts JSON para JS -->
<script type="application/json" id="insumos-json">
    {!! json_encode($insumos ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
</script>

<script type="application/json" id="detalles-json-inicial">
    {!! json_encode($detallesJson ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
</script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    const RUTA_COMPRAS_STORE = "{{ route('compras.store') }}";
</script>

<form id="compra-form" class="space-y-6">
    <!-- Selección de proveedor y fecha -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="proveedor" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Proveedor</label>
            <select id="proveedor" name="proveedor_id"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <option value="">Selecciona un proveedor</option>
                @foreach(\App\Models\Suppliers::all() as $prov)
                    <option value="{{ $prov->id }}" @selected(($getRecord()?->proveedor_id ?? '') == $prov->id)>
                        {{ $prov->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="fecha" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Fecha de compra</label>
            <input type="text" id="fecha" name="fecha" value="{{ now()->format('Y-m-d') }}" readonly
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        </div>
    </div>

    <!-- Encabezado tabla con botón Agregar Insumo -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-3">
        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100">Insumos de la compra</h2>
        <button type="button" id="btn-abrir-modal"
            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-400 rounded-lg font-semibold shadow transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Agregar Insumo
        </button>
    </div>

    <!-- Tabla de insumos -->
    <div class="overflow-x-auto rounded-lg shadow mt-2 w-full">
        <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-800">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="w-2/5 px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Insumo</th>
                    <th class="w-1/6 px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Cantidad</th>
                    <th class="w-1/6 px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Precio unitario</th>
                    <th class="w-1/6 px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Subtotal</th>
                    <th class="w-1/6 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-200">Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-insumos-body" class="divide-y divide-gray-200 dark:divide-gray-600">
                <!-- JS llenará los rows aquí -->
            </tbody>
        </table>
    </div>

    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
        Total: <span id="total-compra" class="text-emerald-600 dark:text-emerald-400">$0</span>
    </p>

    <input type="hidden" name="total" id="total-input" value="{{ $compra->total ?? 0 }}">
    <input type="hidden" name="detalles_json" id="detalles-json">

    <button id="btn-probar"
        class="mt-3 w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-2
        text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700
        dark:bg-emerald-500 dark:hover:bg-emerald-400 transition shadow">
        💾 Guardar compra
    </button>
</form>

<!-- Modal Agregar Insumo -->
<div id="modal-agregar-insumo"
    class="fixed inset-0 z-50 hidden bg-black/50 dark:bg-black/60 flex items-center justify-center px-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-md p-6 relative transform transition-transform duration-200 scale-100">
        <button id="btn-cerrar-modal"
            class="absolute top-3 right-3 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white transition">
            ✕
        </button>

        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Agregar Insumo</h2>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-200">Insumo</label>
                <select id="insumo-select"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Selecciona un insumo</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-200">Cantidad</label>
                <input id="cantidad-input" type="number" min="0"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-200">Precio unitario</label>
                <input id="precio-input" type="number" min="0"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-200">Subtotal</label>
                <p id="subtotal-display" class="font-semibold text-gray-900 dark:text-gray-100">$0</p>
            </div>

            <div class="flex justify-end space-x-3 mt-5">
                <button id="btn-cancelar" type="button"
                    class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition">
                    ✕ Cancelar
                </button>

                <button id="btn-agregar" type="button"
                    class="px-4 py-2 text-sm font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500 dark:hover:bg-blue-400 transition shadow">
                    ➕ Agregar
                </button>
            </div>
        </div>
    </div>
</div>
