/**
 * JavaScript Principal - Calculando a Sorte
 * Gerencia anúncios interativos, modais, AJAX e atualizações de UI
 */

const AppSorte = {
    adInterval: null,
    adTimeLeft: 5,
    adTotalTime: 5,
    adCallback: null,

    init() {
        this.setupAdModal();
    },

    setupAdModal() {
        const claimBtn = document.getElementById('ad-claim-btn');
        if (claimBtn) {
            claimBtn.addEventListener('click', () => {
                this.concluirRecompensaAnuncio();
            });
        }
    },

    /**
     * Inicia o modal de anúncio simulado com cronômetro de 5 segundos
     * @param {Function} callback Função chamada após desbloqueio bem-sucedido
     */
    assistirAnuncio(callback = null) {
        this.adCallback = callback;
        const modal = document.getElementById('ad-modal-backdrop');
        const countdown = document.getElementById('ad-countdown');
        const progressBar = document.getElementById('ad-progress-bar');
        const claimBtn = document.getElementById('ad-claim-btn');

        if (!modal) return;

        this.adTimeLeft = 5;
        this.adTotalTime = 5;

        // Reset dos elementos visuais
        countdown.textContent = `Aguarde: ${this.adTimeLeft}s`;
        progressBar.style.width = '0%';
        claimBtn.disabled = true;
        claimBtn.className = 'w-full py-3 rounded-xl font-bold text-sm text-slate-400 bg-slate-800 cursor-not-allowed transition flex items-center justify-center gap-2 shadow-lg';
        claimBtn.innerHTML = '<i class="fa-solid fa-lock"></i><span>Aguarde o anúncio terminar...</span>';

        modal.classList.remove('hidden');

        clearInterval(this.adInterval);
        const startTime = Date.now();
        const duration = this.adTotalTime * 1000;

        this.adInterval = setInterval(() => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(100, (elapsed / duration) * 100);
            const remaining = Math.max(0, Math.ceil((duration - elapsed) / 1000));

            progressBar.style.width = `${progress}%`;
            countdown.textContent = `Aguarde: ${remaining}s`;

            if (elapsed >= duration) {
                clearInterval(this.adInterval);
                countdown.textContent = 'Completo!';
                countdown.className = 'text-xs font-mono font-bold text-emerald-400 bg-emerald-950/60 px-2 py-1 rounded-lg border border-emerald-500/30';
                
                claimBtn.disabled = false;
                claimBtn.className = 'w-full py-3 rounded-xl font-bold text-sm text-slate-950 bg-gradient-to-r from-amber-400 to-yellow-300 hover:from-amber-300 hover:to-yellow-200 cursor-pointer transition flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20 transform hover:scale-[1.02] active:scale-[0.98] animate-pulse';
                claimBtn.innerHTML = '<i class="fa-solid fa-gift text-slate-950"></i><span>RESGATAR DESBLOQUEIO & MOEDAS</span>';
            }
        }, 50);
    },

    async concluirRecompensaAnuncio() {
        const claimBtn = document.getElementById('ad-claim-btn');
        claimBtn.disabled = true;
        claimBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Processando recompensa...</span>';

        try {
            const response = await fetch('actions/ad_reward.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await response.json();

            // Fecha o modal de anúncio
            document.getElementById('ad-modal-backdrop')?.classList.add('hidden');

            if (data.success) {
                showToast('success', data.message);
                
                // Atualiza moedas na barra se existir
                const navCoins = document.getElementById('nav-coins');
                if (navCoins && data.novo_saldo !== undefined) {
                    navCoins.textContent = Number(data.novo_saldo).toLocaleString();
                }

                // Se houver callback (ex: calculadora desbloquear os botões imediatamente)
                if (typeof this.adCallback === 'function') {
                    this.adCallback(data);
                } else {
                    // Recarrega após breve intervalo se não houver callback específico
                    setTimeout(() => window.location.reload(), 1200);
                }
            } else {
                showToast('error', data.message || 'Erro ao resgatar recompensa.');
            }
        } catch (error) {
            console.error('Erro ao processar anúncio:', error);
            showToast('error', 'Falha na comunicação com o servidor.');
        }
    },

    /**
     * Aciona o upgrade para plano VIP Premium
     * @param {string} metodo 'moedas' ou 'instantaneo'
     */
    solicitarPremium(metodo = 'moedas') {
        const mensagem = metodo === 'moedas' 
            ? 'Deseja usar 150 moedas da sua conta para ativar o VIP Premium Permanente? Todas as 4 operações (+, -, *, /) serão liberadas para sempre!'
            : 'Deseja ativar o Modo VIP Premium de demonstração agora? Todas as operações serão desbloqueadas permanentemente!';

        confirmModal(mensagem, async () => {
            try {
                const response = await fetch('actions/premium_upgrade.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ metodo: metodo })
                });
                const data = await response.json();

                if (data.success) {
                    showToast('success', data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    showToast('warning', data.message);
                }
            } catch (err) {
                console.error(err);
                showToast('error', 'Erro ao conectar ao servidor para upgrade.');
            }
        }, null, 'Ativar VIP Premium');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AppSorte.init();
});
