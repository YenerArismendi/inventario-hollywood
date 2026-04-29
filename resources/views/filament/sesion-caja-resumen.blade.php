@php /** @var \App\Models\SesionCaja $sesion */ @endphp

<div>
    <h2>Resumen de la Sesión de Caja</h2>
    <ul>
        <li><strong>Caja:</strong> {{ $sesion->caja->nombre ?? '-' }}</li>
        <li><strong>Responsable:</strong> {{ $sesion->user->name ?? '-' }}</li>
        <li><strong>Fecha Apertura:</strong> {{ $sesion->fecha_apertura }}</li>
        <li><strong>Fecha Cierre:</strong> {{ $sesion->fecha_cierre }}</li>
        <li><strong>Monto Inicial:</strong> ${{ number_format($sesion->monto_inicial, 0, ',', '.') }}</li>
        <li><strong>Monto Final Calculado:</strong> ${{ number_format($sesion->monto_final_calculado, 0, ',', '.') }}</li>
        <li><strong>Monto Final Contado:</strong> ${{ number_format($sesion->monto_final_contado, 0, ',', '.') }}</li>
        <li><strong>Diferencia:</strong> ${{ number_format($sesion->diferencia, 0, ',', '.') }}</li>
        <li><strong>Estado:</strong> {{ ucfirst($sesion->estado) }}</li>
    </ul>
    <hr>
    <h3>Productos vendidos en la sesión</h3>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%;margin-bottom:1em;">
        <thead>
            <tr>
                <th>Fecha Venta</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Descuento</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        @php
            $hayDetalles = false;
        @endphp
        @foreach($sesion->ventas as $venta)
            @foreach($venta->detalles as $detalle)
                @php $hayDetalles = true; @endphp
                <tr>
                    <td>{{ $venta->created_at }}</td>
                    <td>{{ $detalle->article->nombre ?? '-' }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                    <td>${{ number_format($detalle->descuento_item, 0, ',', '.') }}</td>
                    <td>${{ number_format($detalle->subtotal_item, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        @endforeach
        @unless($hayDetalles)
            <tr>
                <td colspan="6" style="text-align:center;">No hay productos vendidos en esta sesión.</td>
            </tr>
        @endunless
        </tbody>
    </table>
</div>
