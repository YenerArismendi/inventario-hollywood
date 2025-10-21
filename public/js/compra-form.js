document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Script de Compras cargado correctamente.');

    console.log(COMPRA_ID)
    // === Función para mostrar notificaciones con Toastify ===
    const showToast = (message, type = 'info') => {
        if (typeof Toastify === 'undefined') {
            console.error('❌ Toastify no está cargado. Verifica su importación.');
            alert(message);
            return;
        }

        const colors = {
            success: 'linear-gradient(135deg, #16a34a, #4ade80)',   // Verde
            error: 'linear-gradient(135deg, #dc2626, #ef4444)',     // Rojo
            warning: 'linear-gradient(135deg, #facc15, #eab308)',   // Amarillo
            info: 'linear-gradient(135deg, #2563eb, #3b82f6)',      // Azul
        };

        Toastify({
            text: message,
            duration: 4000,
            gravity: 'top',
            position: 'right',
            close: true,
            stopOnFocus: true,
            style: {
                background: colors[type] || colors.info,
                borderRadius: '12px',
                boxShadow: '0 4px 20px rgba(0,0,0,0.15)',
                fontSize: '15px',
                fontFamily: "'Inter', sans-serif",
                padding: '12px 18px',
            }
        }).showToast();
    };

    // === Obtener datos desde Blade ===
    const insumos = JSON.parse(document.getElementById('insumos-json')?.textContent || '[]');
    const detallesExistentes = JSON.parse(document.getElementById('detalles-json-inicial')?.textContent || '[]');

    // === Referencias DOM ===
    const insumoSelect = document.getElementById('insumo-select');
    const btnAbrir = document.getElementById('btn-abrir-modal');
    const btnCerrar = document.getElementById('btn-cerrar-modal');
    const btnCancelar = document.getElementById('btn-cancelar');
    const btnAgregar = document.getElementById('btn-agregar');
    const btnGuardar = document.getElementById('btn-probar');
    const modal = document.getElementById('modal-agregar-insumo');
    const cantidadInput = document.getElementById('cantidad-input');
    const precioInput = document.getElementById('precio-input');
    const subtotalDisplay = document.getElementById('subtotal-display');
    const tablaBody = document.getElementById('tabla-insumos-body');
    const totalDisplay = document.getElementById('total-compra');
    const totalInput = document.getElementById('total-input');
    const detallesJsonInput = document.getElementById('detalles-json');
    const proveedorSelect = document.getElementById('proveedor');
    const fechaInput = document.getElementById('fecha');

    // === Datos iniciales ===
    let insumosAgregados = detallesExistentes.map(d => ({
        id: d.insumo_id,
        nombre: d.nombre ?? '',
        cantidad: d.cantidad,
        precio: d.costo_unitario,
        subtotal: d.costo_total
    }));

    // === Formato COP ===
    const formatoCOP = valor =>
        new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        }).format(valor);

    // === Renderizar tabla ===
    function renderTabla() {
        tablaBody.innerHTML = '';
        let total = 0;

        insumosAgregados.forEach((item, index) => {
            total += item.subtotal;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-2 py-1 border-b">${item.nombre}</td>
                <td class="px-2 py-1 border-b text-center">${item.cantidad}</td>
                <td class="px-2 py-1 border-b text-right">${formatoCOP(item.precio)}</td>
                <td class="px-2 py-1 border-b text-right">${formatoCOP(item.subtotal)}</td>
                <td class="px-2 py-1 border-b text-center">
                    <button data-index="${index}" class="btn-eliminar px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                        Eliminar
                    </button>
                </td>
            `;
            tablaBody.appendChild(row);
        });

        totalDisplay.textContent = formatoCOP(total);
        totalInput.value = total;

        detallesJsonInput.value = JSON.stringify(
            insumosAgregados.map(i => ({
                insumo_id: i.id,
                cantidad: i.cantidad,
                costo_unitario: i.precio,
                costo_total: i.subtotal
            }))
        );

        // ✅ Reasignar eventos de eliminación
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', e => {
                const idx = e.target.dataset.index;
                insumosAgregados.splice(idx, 1);
                renderTabla();
                showToast('🗑️ Insumo eliminado.', 'warning');
            });
        });
    }

    renderTabla();

    // === Llenar select de insumos ===
    if (Array.isArray(insumos) && insumos.length > 0) {
        insumoSelect.innerHTML = '<option value="">Selecciona un insumo</option>';
        insumos.forEach(i => {
            const option = document.createElement('option');
            option.value = i.id;
            option.textContent = i.nombre;
            insumoSelect.appendChild(option);
        });
    }

    // === Subtotal dinámico ===
    const calcularSubtotal = () => {
        const cantidad = parseFloat(cantidadInput.value) || 0;
        const precio = parseFloat(precioInput.value) || 0;
        subtotalDisplay.textContent = formatoCOP(cantidad * precio);
    };
    [cantidadInput, precioInput].forEach(input => input.addEventListener('input', calcularSubtotal));

    // === Abrir y cerrar modal ===
    btnAbrir?.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    [btnCerrar, btnCancelar].forEach(btn =>
        btn?.addEventListener('click', () => {
            modal.classList.add('hidden');
            cantidadInput.value = '';
            precioInput.value = '';
            subtotalDisplay.textContent = '$0';
            insumoSelect.value = '';
        })
    );

    // === Agregar insumo ===
    btnAgregar?.addEventListener('click', () => {
        const insumoId = insumoSelect.value;
        const nombre = insumoSelect.options[insumoSelect.selectedIndex]?.textContent || '';
        const cantidad = parseFloat(cantidadInput.value) || 0;
        const precio = parseFloat(precioInput.value) || 0;
        const subtotal = cantidad * precio;

        if (!insumoId || cantidad <= 0 || precio <= 0) {
            showToast('❌ Selecciona un insumo y completa cantidad y precio válidos.', 'error');
            return;
        }

        // ⚠️ Evitar duplicados
        if (insumosAgregados.some(i => i.id === insumoId)) {
            showToast('⚠️ Este insumo ya fue agregado.', 'warning');
            return;
        }

        insumosAgregados.push({ id: insumoId, nombre, cantidad, precio, subtotal });
        renderTabla();

        cantidadInput.value = '';
        precioInput.value = '';
        subtotalDisplay.textContent = '$0';
        insumoSelect.value = '';
        modal.classList.add('hidden');

        showToast(`✅ ${nombre} agregado correctamente.`, 'success');
    });

    // === Guardar compra ===
    btnGuardar?.addEventListener('click', async (e) => {
        e.preventDefault();

        if (insumosAgregados.length === 0) {
            showToast('⚠️ Debes agregar al menos un insumo antes de guardar.', 'warning');
            return;
        }

        const proveedorId = proveedorSelect?.value;
        const fecha = fechaInput?.value;

        if (!proveedorId || proveedorId === '') {
            showToast('⚠️ Selecciona un proveedor válido.', 'warning');
            return;
        }

        const data = {
            proveedor_id: proveedorId,
            fecha: fecha,
            total: parseFloat(totalInput.value) || 0,
            detalles: insumosAgregados.map(i => ({
                insumo_id: i.id,
                cantidad: i.cantidad,
                costo_unitario: i.precio,
                costo_total: i.subtotal
            }))
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const url = COMPRA_ID ? RUTA_COMPRAS_UPDATE : RUTA_COMPRAS_STORE;
            const method = COMPRA_ID ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showToast('🎉 Compra guardada correctamente.', 'success');
                setTimeout(() => {
                    window.location.href = '/admin/compras'; // 🔹 Redirige al listado de Filament
                }, 1000);
            } else {
                showToast(result.message || '❌ Error al guardar la compra.', 'error');
            }
        } catch (error) {
            console.error(error);
            showToast('🚫 Error de conexión con el servidor.', 'error');
        }
    });
});
