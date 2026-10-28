<?php
/**
 * Componente Visual de Notificação TOAST
 * Exibe mensagens de feedback (sucesso, erro, alerta, info)
 * Calculando a Sorte
 */

$flashToast = getToast();
?>

<!-- Container Global de Toasts -->
<div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full px-4 sm:px-0 pointer-events-none transition-all duration-300">
    <!-- Toasts serão injetados dinamicamente via JS ou renderizados abaixo pelo PHP -->
    <?php if ($flashToast): ?>
        <div class="toast-item pointer-events-auto transform transition-all duration-300 ease-out translate-y-0 opacity-100 flex items-center p-4 rounded-xl shadow-2xl border text-sm font-medium
            <?php 
                switch ($flashToast['tipo']) {
                    case 'success':
                        echo 'bg-emerald-900/90 border-emerald-500/50 text-emerald-100 backdrop-blur-md';
                        break;
                    case 'error':
                        echo 'bg-rose-900/90 border-rose-500/50 text-rose-100 backdrop-blur-md';
                        break;
                    case 'warning':
                        echo 'bg-amber-900/90 border-amber-500/50 text-amber-100 backdrop-blur-md';
                        break;
                    default:
                        echo 'bg-indigo-900/90 border-indigo-500/50 text-indigo-100 backdrop-blur-md';
                        break;
                }
            ?>">
            <div class="mr-3 text-lg flex-shrink-0">
                <?php 
                    switch ($flashToast['tipo']) {
                        case 'success':
                            echo '<i class="fa-solid fa-circle-check text-emerald-400"></i>';
                            break;
                        case 'error':
                            echo '<i class="fa-solid fa-circle-exclamation text-rose-400"></i>';
                            break;
                        case 'warning':
                            echo '<i class="fa-solid fa-triangle-exclamation text-amber-400"></i>';
                            break;
                        default:
                            echo '<i class="fa-solid fa-circle-info text-indigo-400"></i>';
                            break;
                    }
                ?>
            </div>
            <div class="flex-1 pr-2">
                <?= htmlspecialchars($flashToast['mensagem']) ?>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-white/60 hover:text-white transition-colors p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.querySelector('.toast-item');
                if (toast) {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 4500);
        </script>
    <?php endif; ?>
</div>

<script>
/**
 * Dispara uma notificação TOAST na interface do usuário
 * @param {string} tipo 'success' | 'error' | 'warning' | 'info'
 * @param {string} mensagem Texto da notificação
 */
function showToast(tipo, mensagem) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast-item pointer-events-auto transform transition-all duration-300 ease-out translate-y-4 opacity-0 flex items-center p-4 rounded-xl shadow-2xl border text-sm font-medium';

    let iconClass = 'fa-circle-info text-indigo-400';
    let styleClass = 'bg-indigo-900/90 border-indigo-500/50 text-indigo-100 backdrop-blur-md';

    if (tipo === 'success') {
        iconClass = 'fa-circle-check text-emerald-400';
        styleClass = 'bg-emerald-900/90 border-emerald-500/50 text-emerald-100 backdrop-blur-md';
    } else if (tipo === 'error') {
        iconClass = 'fa-circle-exclamation text-rose-400';
        styleClass = 'bg-rose-900/90 border-rose-500/50 text-rose-100 backdrop-blur-md';
    } else if (tipo === 'warning') {
        iconClass = 'fa-triangle-exclamation text-amber-400';
        styleClass = 'bg-amber-900/90 border-amber-500/50 text-amber-100 backdrop-blur-md';
    }

    toast.className += ' ' + styleClass;
    toast.innerHTML = `
        <div class="mr-3 text-lg flex-shrink-0">
            <i class="fa-solid ${iconClass}"></i>
        </div>
        <div class="flex-1 pr-2 leading-snug">${mensagem}</div>
        <button type="button" class="text-white/60 hover:text-white transition-colors p-1 close-btn">
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;

    toast.querySelector('.close-btn').addEventListener('click', () => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    });

    container.appendChild(toast);

    // Animação de entrada
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    // Auto-remover após 4.5 segundos
    setTimeout(() => {
        if (toast.parentElement) {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }
    }, 4500);
}
</script>
