<?php
/**
 * Página de Planos e Adesão VIP Premium
 * Calculando a Sorte
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

$user = null;
if (isset($pdo) && isLoggedIn()) {
    $user = getLoggedUserData($pdo);
}

$pageTitle = "Seja VIP Premium";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="max-w-5xl mx-auto px-4 py-8 sm:py-12">
    
    <!-- Topo -->
    <div class="text-center max-w-2xl mx-auto mb-12">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-bold uppercase tracking-wider mb-3">
            <i class="fa-solid fa-crown text-amber-400"></i> Desbloqueio Ilimitado
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Liberte Todo o Poder Matemático</h1>
        <p class="text-sm text-slate-400 mt-2">Diga adeus às operações bloqueadas! Escolha como deseja liberar as 4 operações básicas na calculadora.</p>
    </div>

    <!-- Comparativo de Planos (Mobile-First) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 max-w-4xl mx-auto">
        
        <!-- PLANO 1: GRATUITO (COM ANÚNCIOS) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 flex flex-col justify-between relative">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">Plano Básico</h2>
                        <p class="text-xs text-slate-400">Jogue com a sorte e desbloqueie com anúncios</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700">
                        Grátis
                    </span>
                </div>

                <div class="my-6">
                    <span class="text-3xl font-black text-white font-mono">R$ 0</span>
                    <span class="text-xs text-slate-500">/ sempre</span>
                </div>

                <ul class="space-y-3 text-xs text-slate-300 border-t border-slate-800/80 pt-6">
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-check text-emerald-400"></i>
                        <span>Uso completo dos números (0 a 9)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-dice text-indigo-400"></i>
                        <span>1 operação sorteada por vez (+, -, * ou /)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-tv text-amber-400"></i>
                        <span>Desbloqueio temporário assistindo a anúncios de 5s</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-coins text-amber-400"></i>
                        <span>Ganha +2 moedas por cálculo</span>
                    </li>
                    <li class="flex items-center gap-2.5 text-slate-500">
                        <i class="fa-solid fa-xmark text-slate-600"></i>
                        <span>Sem operações fixas liberadas</span>
                    </li>
                </ul>
            </div>

            <div class="mt-8 pt-4">
                <?php if ($user): ?>
                    <button type="button" onclick="AppSorte.assistirAnuncio(() => { showToast('success', 'Todas as operações liberadas!'); setTimeout(() => window.location.href='calculadora.php', 1000); })" class="w-full py-3 rounded-xl font-bold text-xs text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-tv text-amber-400"></i>
                        <span>Ver Anúncio Agora (Liberar Grátis)</span>
                    </button>
                <?php else: ?>
                    <a href="login.php" class="w-full py-3 rounded-xl font-bold text-xs text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 transition flex items-center justify-center gap-2">
                        <span>Entrar para Jogar Grátis</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- PLANO 2: VIP PREMIUM -->
        <div class="bg-gradient-to-b from-slate-900 via-slate-900 to-amber-950/30 border-2 border-amber-500/50 rounded-3xl p-6 sm:p-8 flex flex-col justify-between relative shadow-2xl shadow-amber-500/10">
            <!-- Badge Mais Popular -->
            <div class="absolute -top-3.5 right-6 px-3 py-1 rounded-full bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 text-[10px] font-black uppercase tracking-wider shadow-lg">
                Recomendado
            </div>

            <div>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <span>VIP Premium</span>
                            <i class="fa-solid fa-crown text-amber-400 text-sm"></i>
                        </h2>
                        <p class="text-xs text-amber-300/80">Acesso definitivo e sem restrições</p>
                    </div>
                </div>

                <div class="my-6">
                    <span class="text-3xl font-black text-amber-400 font-mono">150 Moedas</span>
                    <span class="text-xs text-slate-400">ou Ativação Direta</span>
                </div>

                <ul class="space-y-3 text-xs text-slate-200 border-t border-slate-800/80 pt-6">
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-check text-amber-400"></i>
                        <span class="font-bold text-white">Todas as 4 operações (+, −, ×, ÷) liberadas para sempre!</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-ban text-amber-400"></i>
                        <span>Zero bloqueios ou necessidade de anúncios</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-trophy text-amber-400"></i>
                        <span>Badge VIP exclusiva no perfil e ranking</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i>
                        <span>Histórico e auditoria ilimitados</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-bolt text-amber-400"></i>
                        <span>Acesso prioritário a novos jogos matemáticos</span>
                    </li>
                </ul>
            </div>

            <div class="mt-8 pt-4 space-y-2">
                <?php if ($user && $user['is_premium']): ?>
                    <div class="p-3 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 text-center font-bold text-xs flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Você já é um Membro VIP Premium Ativo!</span>
                    </div>
                <?php elseif ($user): ?>
                    <button type="button" onclick="AppSorte.solicitarPremium('moedas')" class="w-full py-3.5 rounded-xl font-bold text-xs text-slate-950 bg-gradient-to-r from-amber-400 to-yellow-300 hover:from-amber-300 hover:to-yellow-200 transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-coins text-slate-950"></i>
                        <span>Ativar com 150 Moedas (Saldo: <?= $user['moedas'] ?>)</span>
                    </button>
                    <button type="button" onclick="AppSorte.solicitarPremium('instantaneo')" class="w-full py-2.5 rounded-xl font-bold text-xs text-amber-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>Ativar VIP Imediato (Modo Demo)</span>
                    </button>
                <?php else: ?>
                    <a href="login.php" class="w-full py-3.5 rounded-xl font-bold text-xs text-slate-950 bg-gradient-to-r from-amber-400 to-yellow-300 hover:from-amber-300 hover:to-yellow-200 transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                        <span>Entrar para Assinar VIP</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
