<?php
/**
 * Barra de Navegação Responsiva (Mobile-First)
 * Calculando a Sorte
 */
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>

<nav class="sticky top-0 z-40 bg-slate-950/80 backdrop-blur-md border-b border-slate-800/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            <!-- Logo e Marca -->
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-amber-400 p-0.5 shadow-lg group-hover:scale-105 transition transform">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                        <i class="fa-solid fa-dice-d20 text-indigo-400 text-lg group-hover:rotate-12 transition transform"></i>
                    </div>
                </div>
                <div>
                    <span class="text-lg font-black tracking-tight text-white flex items-center gap-1">
                        Calculando<span class="text-amber-400">Sorte</span>
                    </span>
                    <span class="text-[10px] block font-semibold text-indigo-400 tracking-wider uppercase -mt-1">
                        Math & Fortuna
                    </span>
                </div>
            </a>

            <!-- Links Desktop -->
            <div class="hidden md:flex items-center gap-1">
                <a href="index.php" class="px-3 py-2 rounded-xl text-sm font-semibold transition <?= $currentPage === 'index.php' ? 'text-white bg-slate-800/80 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' ?>">
                    <i class="fa-solid fa-house mr-1.5 text-xs text-indigo-400"></i>Início
                </a>
                <a href="calculadora.php" class="px-3 py-2 rounded-xl text-sm font-semibold transition <?= $currentPage === 'calculadora.php' ? 'text-white bg-slate-800/80 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' ?>">
                    <i class="fa-solid fa-calculator mr-1.5 text-xs text-amber-400"></i>Calculadora da Sorte
                </a>
                <a href="jogos.php" class="px-3 py-2 rounded-xl text-sm font-semibold transition <?= $currentPage === 'jogos.php' ? 'text-white bg-slate-800/80 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' ?>">
                    <i class="fa-solid fa-gamepad mr-1.5 text-xs text-emerald-400"></i>Jogos
                </a>
                <a href="historico.php" class="px-3 py-2 rounded-xl text-sm font-semibold transition <?= $currentPage === 'historico.php' ? 'text-white bg-slate-800/80 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' ?>">
                    <i class="fa-solid fa-clipboard-list mr-1.5 text-xs text-purple-400"></i>Histórico & Auditoria
                </a>
                <a href="premium.php" class="px-3 py-2 rounded-xl text-sm font-semibold transition <?= $currentPage === 'premium.php' ? 'text-amber-300 bg-amber-500/10 border border-amber-500/30' : 'text-amber-400 hover:text-amber-300 hover:bg-amber-500/10' ?>">
                    <i class="fa-solid fa-crown mr-1.5 text-xs text-amber-400"></i>Premium
                </a>
            </div>

            <!-- Área do Usuário Desktop -->
            <div class="hidden md:flex items-center gap-3">
                <?php if ($currentUser): ?>
                    <!-- Moedas -->
                    <div class="flex items-center gap-2 bg-slate-900/90 border border-amber-500/20 px-3 py-1.5 rounded-xl shadow-inner">
                        <i class="fa-solid fa-coins text-amber-400 text-sm animate-pulse"></i>
                        <span class="text-xs font-bold text-amber-300 font-mono" id="nav-coins"><?= number_format($currentUser['moedas']) ?></span>
                    </div>

                    <!-- Badge VIP / Normal -->
                    <?php if ($currentUser['is_premium']): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500/20 to-yellow-500/20 text-amber-300 border border-amber-500/40">
                            <i class="fa-solid fa-crown text-[10px]"></i> VIP
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                            Free
                        </span>
                    <?php endif; ?>

                    <!-- Menu Usuário -->
                    <div class="relative group">
                        <a href="perfil.php" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700/80 text-sm font-semibold text-slate-200 hover:border-indigo-500/50 transition">
                            <div class="w-6 h-6 rounded-full bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-xs text-indigo-300">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="truncate max-w-[100px]"><?= htmlspecialchars(explode(' ', $currentUser['nome'])[0]) ?></span>
                        </a>
                    </div>

                    <!-- Botão de Sair com Modal -->
                    <button type="button" onclick="confirmarLogout()" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Sair da Conta">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                <?php else: ?>
                    <a href="login.php" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                        Entrar
                    </a>
                    <a href="register.php" class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition">
                        Criar Conta
                    </a>
                <?php endif; ?>
            </div>

            <!-- Botão Menu Mobile (Mobile-First) -->
            <div class="flex md:hidden items-center gap-2">
                <?php if ($currentUser): ?>
                    <div class="flex items-center gap-1.5 bg-slate-900 border border-amber-500/20 px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-coins text-amber-400 text-xs"></i>
                        <span class="text-xs font-bold text-amber-300 font-mono"><?= number_format($currentUser['moedas']) ?></span>
                    </div>
                <?php endif; ?>

                <button id="mobile-menu-btn" type="button" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white transition">
                    <i class="fa-solid fa-bars text-lg" id="mobile-menu-icon"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- Gaveta de Navegação Mobile -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-800/80 bg-slate-950/95 backdrop-blur-xl px-4 pt-3 pb-6 space-y-2">
        <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= $currentPage === 'index.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-300 hover:bg-slate-900' ?>">
            <i class="fa-solid fa-house w-5 text-center text-indigo-400"></i> Início
        </a>
        <a href="calculadora.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= $currentPage === 'calculadora.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-300 hover:bg-slate-900' ?>">
            <i class="fa-solid fa-calculator w-5 text-center text-amber-400"></i> Calculadora da Sorte
        </a>
        <a href="jogos.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= $currentPage === 'jogos.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-300 hover:bg-slate-900' ?>">
            <i class="fa-solid fa-gamepad w-5 text-center text-emerald-400"></i> Jogos Matemáticos
        </a>
        <a href="historico.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= $currentPage === 'historico.php' ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'text-slate-300 hover:bg-slate-900' ?>">
            <i class="fa-solid fa-clipboard-list w-5 text-center text-purple-400"></i> Histórico & Auditoria
        </a>
        <a href="premium.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-gradient-to-r from-amber-500/10 to-yellow-500/10 text-amber-300 border border-amber-500/30">
            <i class="fa-solid fa-crown w-5 text-center text-amber-400"></i> Desbloquear Premium
        </a>

        <div class="pt-3 border-t border-slate-800 space-y-2">
            <?php if ($currentUser): ?>
                <a href="perfil.php" class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm font-medium text-slate-200">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-user-circle text-indigo-400"></i>
                        <span><?= htmlspecialchars($currentUser['nome']) ?></span>
                    </span>
                    <span class="text-xs text-slate-400"><?= $currentUser['is_premium'] ? 'VIP' : 'Free' ?></span>
                </a>
                <button type="button" onclick="confirmarLogout()" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-sm font-semibold text-rose-400 bg-rose-500/10 border border-rose-500/20">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Sair da Conta
                </button>
            <?php else: ?>
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="login.php" class="text-center py-2.5 rounded-xl text-sm font-semibold bg-slate-900 border border-slate-700 text-white">
                        Entrar
                    </a>
                    <a href="register.php" class="text-center py-2.5 rounded-xl text-sm font-bold bg-indigo-600 text-white shadow-lg shadow-indigo-600/30">
                        Cadastre-se
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
// Toggle menu mobile
document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    const icon = document.getElementById('mobile-menu-icon');
    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-xmark');
    } else {
        menu.classList.add('hidden');
        icon.classList.remove('fa-xmark');
        icon.classList.add('fa-bars');
    }
});

// Confirmação de Logout usando MODAL conforme requisitos
function confirmarLogout() {
    confirmModal(
        'Tem certeza que deseja encerrar sua sessão atual? Todos os seus dados e histórico foram salvos com segurança.',
        function() {
            window.location.href = 'actions/auth_logout.php';
        },
        null,
        'Confirmar Saída'
    );
}
</script>
