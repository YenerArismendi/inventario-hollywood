<!-- ✅ Toastify desde CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<style>
    /* --- Estilo del toast --- */
    .toastify {
        backdrop-filter: blur(10px);
        background: rgba(30, 30, 30, 0.7) !important;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px !important;
        font-size: 1.05rem;
        padding: 1rem 1.5rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 320px;
        overflow: hidden;
        position: relative;
        z-index: 9999;
        pointer-events: all;
    }

    /* --- Barra de progreso --- */
    .toastify::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        height: 5px;
        width: 100%;
        animation: progressBar 4s linear forwards;
        border-radius: 0 0 14px 14px;
    }

    .toast-success::after {
        background: linear-gradient(to right, #00c853, #b2ff59);
    }

    .toast-error::after {
        background: linear-gradient(to right, #ff1744, #d50000);
    }

    .toast-warning::after {
        background: linear-gradient(to right, #ff9800, #ffb74d);
    }

    @keyframes progressBar {
        from { width: 100%; }
        to { width: 0%; }
    }

    /* --- Contenedor global forzado --- */
    .toastify-right.toastify-top {
        position: fixed !important;
        top: 1.2rem !important;
        right: 1.2rem !important;
        left: auto !important;
        z-index: 99999 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.showToast = (type, message) => {
            let bgClass = 'toast-success';
            let icon = '<i class="fa-solid fa-check-circle"></i>';

            switch (type) {
                case 'error':
                    bgClass = 'toast-error';
                    icon = '<i class="fa-solid fa-circle-xmark"></i>';
                    break;
                case 'warning':
                    bgClass = 'toast-warning';
                    icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
                    break;
            }

            Toastify({
                text: `${icon} ${message}`,
                className: bgClass,
                gravity: "top",
                position: "right", // ✅ Mantiene posición derecha
                stopOnFocus: true,
                escapeMarkup: false, // Permite HTML (íconos)
                duration: 4000,
                offset: {
                    x: 20,
                    y: 20
                },
                style: {
                    background: "rgba(30, 30, 30, 0.7)",
                    borderRadius: "14px",
                    boxShadow: "0 8px 24px rgba(0,0,0,0.25)",
                    color: "#fff",
                }
            }).showToast();
        };

        console.log("✅ Sistema de alertas Toastify listo (posición derecha confirmada)");
    });
</script>

<!-- FontAwesome para íconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
