<?php
/**
 * Página Inicial / Dashboard
 * Calculando a Sorte
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

$user = null;
if (isset($pdo) && isLoggedIn()) {
    $user = getLoggedUserData($pdo);
}

$pageTitle = "A Plataforma de Matemática e Sorte";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="relative overflow-hidden py-10 sm:py-16">
    
    <!-- Efeitos de Luz no Fundo -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-gradient-to-tr from-indigo-600/20 via-purple-600/20 to-amber-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Hero Section (Mobile-First) -->
        <div class="text-center max-w-3xl mx-auto space-y-6">
            
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900 border border-slate-700/80 text-xs font-bold text-slate-300 shadow-xl">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                <span class="text-amber-400">Novo Conceito:</span>
                <span>Calculadora + Roleta da Sorte + Minijogos</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.15]">
                Calcule, Jogue e Teste a sua <span class="bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500 bg-clip-text text-transparent">Sorte Matemática</span>
            </h1>

            <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Uma calculadora dinâmica onde os números funcionam normalmente e as <strong class="text-white">operações básicas (+, −, ×, ÷) são sorteadas</strong>. Desbloqueie todas assistindo a anúncios ou torne-se um membro VIP!
            </p>

            <!-- Ações CTA -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <?php if ($user): ?>
                    <a href="calculadora.php" class="w-full sm:w-auto px-8 py-4 rounded-2xl font-black text-sm text-slate-950 bg-gradient-to-r from-amber-400 to-yellow-300 hover:from-amber-300 hover:to-yellow-200 shadow-xl shadow-amber-500/25 transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-calculator text-base"></i>
                        <span>Abrir Minha Calculadora da Sorte</span>
                    </a>
                    <a href="jogos.php" class="w-full sm:w-auto px-6 py-4 rounded-2xl font-bold text-sm text-white bg-slate-800/80 hover:bg-slate-700 border border-slate-700 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-gamepad text-emerald-400"></i>
                        <span>Arena de Jogos</span>
                    </a>
                <?php else: ?>
                    <a href="register.php" class="w-full sm:w-auto px-8 py-4 rounded-2xl font-black text-sm text-slate-950 bg-gradient-to-r from-amber-400 to-yellow-300 hover:from-amber-300 hover:to-yellow-200 shadow-xl shadow-amber-500/25 transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-play"></i>
                        <span>Jogar Grátis com 50 Moedas</span>
                    </a>
                    <a href="login.php" class="w-full sm:w-auto px-6 py-4 rounded-2xl font-bold text-sm text-white bg-slate-800/80 hover:bg-slate-700 border border-slate-700 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket text-indigo-400"></i>
                        <span>Já tenho conta (Entrar)</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Demonstração Rápida de Usuários de Teste -->
            <div class="pt-2 text-xs text-slate-400">
                <span>Contas de demonstração prontas: </span>
                <a href="login.php" class="text-amber-400 font-semibold underline hover:text-amber-300">jogador@sorte.com</a>
                <span class="text-slate-600">•</span>
                <a href="login.php" class="text-amber-400 font-semibold underline hover:text-amber-300">vip@sorte.com</a>
                <span class="text-slate-500">(senha: 123456)</span>
            </div>

        </div>

        <!-- Grade de Recursos (3 Pilares Principais) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 sm:mt-24">
            
            <!-- Card 1 -->
            <div class="bg-slate-900/80 border border-slate-800/80 rounded-3xl p-6 sm:p-8 relative overflow-hidden backdrop-blur-xl group hover:border-indigo-500/50 transition">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-xl text-indigo-400 mb-5 group-hover:scale-110 transition">
                    <i class="fa-solid fa-dice-d20"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Calculadora com Roleta da Sorte</h3>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                    Você usa os números livremente. A cada rodada uma operação matemática (+, -, * ou /) é sorteada para você. Gire a roleta e desafie seus cálculos!
                </p>
            </div>

            <!-- Card 2 -->
            <div class="bg-slate-900/80 border border-slate-800/80 rounded-3xl p-6 sm:p-8 relative overflow-hidden backdrop-blur-xl group hover:border-amber-500/50 transition">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-xl text-amber-400 mb-5 group-hover:scale-110 transition">
                    <i class="fa-solid fa-tv"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Desbloqueio por Anúncio ou VIP</h3>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                    Precisa de uma operação que está bloqueada? Assista a um anúncio rápido de 5 segundos para liberar todas e faturar moedas, ou torne-se VIP Premium definitivo!
                </p>
            </div>

            <!-- Card 3 -->
            <div class="bg-slate-900/80 border border-slate-800/80 rounded-3xl p-6 sm:p-8 relative overflow-hidden backdrop-blur-xl group hover:border-emerald-500/50 transition">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-xl text-emerald-400 mb-5 group-hover:scale-110 transition">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">100% Auditável via PDO & Bcrypt</h3>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                    Segurança máxima. Senhas protegidas com hash Bcrypt, queries preparadas contra SQL Injection e painel completo de auditoria para cada ação do sistema.
                </p>
            </div>

        </div>

        <!-- Banner de Jogos Matemáticos Adicionais -->
        <div class="mt-12 bg-gradient-to-r from-indigo-950/60 via-purple-950/40 to-slate-900 border border-indigo-500/30 rounded-3xl p-6 sm:p-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center md:text-left">
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-wider">
                    Arena de Desafios
                </span>
                <h3 class="text-xl sm:text-2xl font-bold text-white">Treine sua mente na Blitz de 45 segundos</h3>
                <p class="text-xs sm:text-sm text-slate-300 max-w-xl">
                    Resolva o máximo de operações matemáticas antes do relógio zerar, suba de posição no ranking de mestres e acumule moedas de ouro da sorte!
                </p>
            </div>
            <a href="jogos.php" class="px-6 py-3.5 rounded-xl font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-500 shadow-xl shadow-indigo-600/30 transition transform hover:scale-105 active:scale-95 flex-shrink-0">
                Conhecer os Jogos →
            </a>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
