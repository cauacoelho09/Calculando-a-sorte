<?php
/**
 * Componente Visual de Modais do Sistema
 * Substitui todas as caixas de mensagens nativas do navegador (alert, confirm)
 * Calculando a Sorte
 */
?>

<!-- Modal Universal de Alerta / Confirmação -->
<div id="custom-modal-backdrop" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
    <div id="custom-modal-card" class="bg-slate-900 border border-slate-700/80 w-full max-w-md rounded-2xl shadow-2xl p-6 transform transition-all duration-300 scale-95 opacity-0">
        <!-- Ícone e Título -->
        <div class="flex items-center gap-4 mb-4">
            <div id="modal-icon-container" class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">
                <i id="modal-icon" class="fa-solid fa-circle-info"></i>
            </div>
            <div>
                <h3 id="modal-title" class="text-lg font-bold text-white">Mensagem</h3>
                <p class="text-xs text-slate-400" id="modal-subtitle">Aviso do Sistema</p>
            </div>
        </div>

        <!-- Conteúdo do Modal -->
        <div id="modal-body" class="text-slate-300 text-sm leading-relaxed mb-6">
            <!-- Mensagem aqui -->
        </div>

        <!-- Botões de Ação -->
        <div class="flex items-center justify-end gap-3" id="modal-actions">
            <button id="modal-btn-cancel" type="button" class="hidden px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800 border border-slate-700 transition">
                Cancelar
            </button>
            <button id="modal-btn-confirm" type="button" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition">
                Entendido
            </button>
        </div>
    </div>
</div>

<!-- Modal Específico: Assistir Anúncio da Sorte -->
<div id="ad-modal-backdrop" class="fixed inset-0 z-50 hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300">
    <div class="bg-gradient-to-b from-slate-900 to-slate-950 border border-amber-500/30 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden p-6 relative">
        <div class="flex justify-between items-center mb-4 border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase">Patrocinado</span>
                <span class="text-xs text-slate-400">Anúncio da Sorte</span>
            </div>
            <div id="ad-countdown" class="text-xs font-mono font-bold text-amber-400 bg-amber-950/60 px-2 py-1 rounded-lg border border-amber-500/20">
                Aguarde: 5s
            </div>
        </div>

        <!-- Conteúdo visual do Anúncio -->
        <div class="relative bg-slate-800/80 rounded-xl p-5 border border-slate-700 text-center mb-5 overflow-hidden">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl"></div>
            <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-tr from-amber-500 to-yellow-300 rounded-2xl flex items-center justify-center text-slate-950 text-2xl shadow-lg shadow-amber-500/20 animate-bounce">
                <i class="fa-solid fa-gem"></i>
            </div>
            <h4 class="text-lg font-bold text-white mb-1">Clube da Matemática Premiada</h4>
            <p class="text-xs text-slate-300 mb-3">Multiplique sua sorte e libere todas as 4 operações de uma vez só!</p>
            <div class="w-full bg-slate-700/60 rounded-full h-2 overflow-hidden mb-2">
                <div id="ad-progress-bar" class="bg-gradient-to-r from-amber-500 to-yellow-400 h-2 w-0 transition-all duration-100 ease-linear"></div>
            </div>
            <p class="text-[11px] text-slate-400">Assista até o fim para desbloquear todas as operações!</p>
        </div>

        <button id="ad-claim-btn" disabled type="button" class="w-full py-3 rounded-xl font-bold text-sm text-slate-400 bg-slate-800 cursor-not-allowed transition flex items-center justify-center gap-2 shadow-lg">
            <i class="fa-solid fa-lock"></i>
            <span>Aguarde o anúncio terminar...</span>
        </button>
    </div>
</div>

<script>
/**
 * Gerenciador dos Modais Customizados
 * Substitui alert() e confirm() nativos
 */
const CustomModal = {
    backdrop: null,
    card: null,
    title: null,
    body: null,
    icon: null,
    iconContainer: null,
    btnConfirm: null,
    btnCancel: null,
    onConfirmCallback: null,
    onCancelCallback: null,

    init() {
        this.backdrop = document.getElementById('custom-modal-backdrop');
        this.card = document.getElementById('custom-modal-card');
        this.title = document.getElementById('modal-title');
        this.body = document.getElementById('modal-body');
        this.icon = document.getElementById('modal-icon');
        this.iconContainer = document.getElementById('modal-icon-container');
        this.btnConfirm = document.getElementById('modal-btn-confirm');
        this.btnCancel = document.getElementById('modal-btn-cancel');

        if (this.btnConfirm) {
            this.btnConfirm.addEventListener('click', () => {
                this.close();
                if (typeof this.onConfirmCallback === 'function') {
                    this.onConfirmCallback();
                }
            });
        }

        if (this.btnCancel) {
            this.btnCancel.addEventListener('click', () => {
                this.close();
                if (typeof this.onCancelCallback === 'function') {
                    this.onCancelCallback();
                }
            });
        }
    },

    open(options = {}) {
        if (!this.backdrop) this.init();

        const {
            title = 'Aviso',
            subtitle = 'Calculando a Sorte',
            message = '',
            type = 'info', // 'info', 'success', 'warning', 'error', 'confirm'
            confirmText = 'OK',
            cancelText = 'Cancelar',
            showCancel = false,
            onConfirm = null,
            onCancel = null
        } = options;

        this.title.textContent = title;
        document.getElementById('modal-subtitle').textContent = subtitle;
        this.body.innerHTML = message;
        this.onConfirmCallback = onConfirm;
        this.onCancelCallback = onCancel;

        this.btnConfirm.textContent = confirmText;
        this.btnCancel.textContent = cancelText;

        if (showCancel) {
            this.btnCancel.classList.remove('hidden');
        } else {
            this.btnCancel.classList.add('hidden');
        }

        // Estilização baseada no tipo
        this.iconContainer.className = 'w-12 h-12 rounded-xl flex items-center justify-center text-xl ';
        if (type === 'success') {
            this.iconContainer.className += 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
            this.icon.className = 'fa-solid fa-circle-check';
            this.btnConfirm.className = 'px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-600/30 transition';
        } else if (type === 'error') {
            this.iconContainer.className += 'bg-rose-500/20 text-rose-400 border border-rose-500/30';
            this.icon.className = 'fa-solid fa-circle-xmark';
            this.btnConfirm.className = 'px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-600 hover:bg-rose-500 shadow-lg shadow-rose-600/30 transition';
        } else if (type === 'warning' || type === 'confirm') {
            this.iconContainer.className += 'bg-amber-500/20 text-amber-400 border border-amber-500/30';
            this.icon.className = 'fa-solid fa-triangle-exclamation';
            this.btnConfirm.className = 'px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-600/30 transition';
        } else {
            this.iconContainer.className += 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30';
            this.icon.className = 'fa-solid fa-circle-info';
            this.btnConfirm.className = 'px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition';
        }

        this.backdrop.classList.remove('hidden');
        requestAnimationFrame(() => {
            this.card.classList.remove('scale-95', 'opacity-0');
            this.card.classList.add('scale-100', 'opacity-100');
        });
    },

    close() {
        if (!this.card) return;
        this.card.classList.remove('scale-100', 'opacity-100');
        this.card.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            this.backdrop.classList.add('hidden');
        }, 200);
    }
};

/**
 * Funções Globais amigáveis para chamadas em qualquer página
 */
function alertModal(mensagem, titulo = 'Atenção', tipo = 'info') {
    CustomModal.open({
        title: titulo,
        message: mensagem,
        type: tipo,
        showCancel: false,
        confirmText: 'Entendido'
    });
}

function confirmModal(mensagem, onConfirm, onCancel = null, titulo = 'Confirmação') {
    CustomModal.open({
        title: titulo,
        message: mensagem,
        type: 'confirm',
        showCancel: true,
        confirmText: 'Confirmar',
        cancelText: 'Cancelar',
        onConfirm: onConfirm,
        onCancel: onCancel
    });
}

// Substituição não-intrusiva do alert nativo do navegador
window.originalAlert = window.alert;
window.alert = function(msg) {
    alertModal(msg, 'Mensagem do Sistema', 'info');
};

document.addEventListener('DOMContentLoaded', () => {
    CustomModal.init();
});
</script>
