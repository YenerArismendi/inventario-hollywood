import { Notyf } from 'notyf'
import 'notyf/notyf.min.css'

// 🎨 Estilos personalizados (tipo Filament)
const notyf = new Notyf({
    duration: 4000,
    position: { x: 'right', y: 'top' },
    ripple: true,
    dismissible: true,
    types: [
        {
            type: 'success',
            background: 'rgba(34,197,94,0.9)',
            icon: {
                className: 'fas fa-check-circle',
                tagName: 'i',
                color: 'white'
            },
            className: 'notyf-success-custom'
        },
        {
            type: 'warning',
            background: 'rgba(234,179,8,0.9)',
            icon: {
                className: 'fas fa-exclamation-triangle',
                tagName: 'i',
                color: 'white'
            },
            className: 'notyf-warning-custom'
        },
        {
            type: 'danger',
            background: 'rgba(239,68,68,0.9)',
            icon: {
                className: 'fas fa-times-circle',
                tagName: 'i',
                color: 'white'
            },
            className: 'notyf-danger-custom'
        },
        {
            type: 'info',
            background: 'rgba(59,130,246,0.9)',
            icon: {
                className: 'fas fa-info-circle',
                tagName: 'i',
                color: 'white'
            },
            className: 'notyf-info-custom'
        },
    ]
})

// 💎 Estilos extra (efecto glassmorphism + sombras suaves)
const style = document.createElement('style')
style.innerHTML = `
    .notyf__toast {
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.2);
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        border-radius: 12px;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
    }

    .notyf__toast i {
        margin-right: 8px;
        font-size: 18px;
    }

    .notyf-success-custom {
        background: linear-gradient(135deg, #22c55e, #15803d);
    }

    .notyf-danger-custom {
        background: linear-gradient(135deg, #ef4444, #991b1b);
    }

    .notyf-warning-custom {
        background: linear-gradient(135deg, #facc15, #ca8a04);
    }

    .notyf-info-custom {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }
`
document.head.appendChild(style)

// 🌍 Hacerlo accesible globalmente
window.notyf = notyf
