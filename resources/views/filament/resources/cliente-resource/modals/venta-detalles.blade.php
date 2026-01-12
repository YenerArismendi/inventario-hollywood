<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <p class="font-bold">Cliente:</p>
            <p>{{ $venta->cliente->nombre }}</p>
        </div>
        <div>
            <p class="font-bold">Fecha:</p>
            <p>{{ $venta->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <p class="font-bold">Método de Pago:</p>
            <p class="capitalize">{{ $venta->metodo_pago }}</p>
        </div>
        <div>
            <p class="font-bold">Total Venta:</p>
            <p>$ {{ number_format($venta->total, 0, ',', '.') }}</p>
        </div>
    </div>

    <table class="w-full text-left text-sm border-collapse">
        <thead>
            <tr class="border-b">
                <th class="py-2">Producto</th>
                <th class="py-2 text-center">Cant.</th>
                <th class="py-2 text-right">Precio Unit.</th>
                <th class="py-2 text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr class="border-b last:border-0">
                <td class="py-2">
                    {{ $detalle->article->nombre }}
                    <span class="text-xs text-gray-500 block">{{ $detalle->article->codigo }}</span>
                </td>
                <td class="py-2 text-center">{{ $detalle->cantidad }}</td>
                <td class="py-2 text-right">$ {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                <td class="py-2 text-right">$ {{ number_format($detalle->subtotal_item, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-bold">
                <td colspan="3" class="py-2 text-right uppercase">Total:</td>
                <td class="py-2 text-right">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>