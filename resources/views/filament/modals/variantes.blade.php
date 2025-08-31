<div class="space-y-4">
    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Lista de Variantes</h2>

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
            <tr>
                <th class="border-b border-gray-200 dark:border-gray-700 px-4 py-2">Medida</th>
                <th class="border-b border-gray-200 dark:border-gray-700 px-4 py-2">Color</th>
                <th class="border-b border-gray-200 dark:border-gray-700 px-4 py-2">Material</th>
                <th class="border-b border-gray-200 dark:border-gray-700 px-4 py-2">Calidad</th>
                <th class="border-b border-gray-200 dark:border-gray-700 px-4 py-2">Creado</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($variantes as $variante)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-2 text-gray-900 dark:text-gray-300">{{ $variante->medida }}</td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $variante->color }}</td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $variante->material }}</td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $variante->calidad }}</td>
                    <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ $variante->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400" colspan="3">
                        No hay variantes registradas.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
