<!-- Toastify -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    /* 🔹 Contenedor fijo en la parte superior derecha */
    #toast-container {
        position: fixed;
        top: 1.2rem;
        right: 1.2rem;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
        z-index: 99999;
        pointer-events: none;
    }

    /* 🔹 Estilo general del toast */
    .custom-toast {
        border-radius: 12px;
        font-size: 1rem;
        padding: 0.9rem 1.3rem;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 320px;
        position: relative;
        overflow: hidden;
        pointer-events: all;
        animation: fadeInRight 0.35s ease-out;
    }

    /* 🔹 Colores por tipo */
    .toast-success {
        background: linear-gradient(135deg, #16a34a, #4ade80);
    }

    .toast-error {
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .toast-warning {
        background: linear-gradient(135deg, #f59e0b, #facc15);
        color: #000;
    }

    /* 🔹 Barra de progreso */
    .custom-toast::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        height: 5px;
        width: 100%;
        animation: progressBar 4s linear forwards;
        border-radius: 0 0 12px 12px;
        opacity: 0.9;
    }

    .toast-success::after {
        background: linear-gradient(to right, #15803d, #86efac);
    }

    .toast-error::after {
        background: linear-gradient(to right, #b91c1c, #f87171);
    }

    .toast-warning::after {
        background: linear-gradient(to right, #ca8a04, #fde047);
    }

    @keyframes progressBar {
        from { width: 100%; }
        to { width: 0%; }
    }

    /* 🔹 Animaciones */
    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(40px);
        }
    }
</style>

<!-- 🔹 Contenedor -->
<div id="toast-container"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    window.showToast = (type, message) => {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.classList.add('custom-toast', `toast-${type}`);

        let icon = '<i class="fa-solid fa-check-circle"></i>';
        if (type === 'error') icon = '<i class="fa-solid fa-circle-xmark"></i>';
        else if (type === 'warning') icon = '<i class="fa-solid fa-triangle-exclamation"></i>';

        toast.innerHTML = `${icon} ${message}`;
        container.appendChild(toast);

        // 🔹 Auto eliminar con animación
        setTimeout(() => {
            toast.style.animation = "fadeOutRight 0.35s ease-in forwards";
            setTimeout(() => toast.remove(), 350);
        }, 4000);
    };

    console.log("✅ Alertas sólidas activadas en la parte superior derecha");
});
</script>
