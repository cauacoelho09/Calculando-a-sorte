<?php
/**
 * Tela Principal da Calculadora da Sorte
 * Calculando a Sorte
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth_check.php';

$user = getLoggedUserData($pdo);
if (!$user) {
    header('Location: login.php');
    exit;
}

// Últimos 6 cálculos do usuário
$stmtUltimos = $pdo->prepare("
    SELECT id, expressao, operacao, resultado, tipo_desbloqueio, created_at 
    FROM calculos 
    WHERE user_id = ? 
    ORDER BY id DESC 
    LIMIT 6
");
$stmtUltimos->execute([$user['id']]);
$ultimosCalculos = $stmtUltimos->fetchAll();

$pageTitle = "Calculadora da Sorte";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="max-w-4xl mx-auto px-4 py-6 sm:py-8">
    
    <!-- Header da Página -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    <i class="fa-solid fa-dice text-amber-400 mr-1"></i> Modo Sorte Ativo
                </span>
                <?php if ($user['is_premium']): ?>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-gradient-to-r from-amber-500/20 to-yellow-500/20 text-amber-300 border border-amber-500/40">
                        <i class="fa-solid fa-crown text-amber-400 mr-1"></i> VIP Premium
                    </span>
                <?php elseif ($user['anuncio_ativo']): ?>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 animate-pulse">
                        <i class="fa-solid fa-clock mr-1"></i> Anúncio Ativo (Todas Liberadas)
                    </span>
                <?php else: ?>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-800 text-slate-400 border border-slate-700">
                        Plano Básico (1 Operação Sorteada)
                    </span>
                <?php endif; ?>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">Calculadora da Sorte</h1>
            <p class="text-xs sm:text-sm text-slate-400">Use os números normalmente e jogue com as operações sorteadas!</p>
        </div>

        <!-- Ações de Desbloqueio e Sorteio -->
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" onclick="sortearOperacao()" id="btn-spin-op" class="px-3.5 py-2.5 rounded-xl font-bold text-xs bg-slate-800 hover:bg-slate-700 text-indigo-300 border border-indigo-500/30 transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-shuffle text-indigo-400 text-sm" id="icon-spin"></i>
                <span>Sortear Nova Operação</span>
            </button>

            <?php if (!$user['is_premium']): ?>
                <button type="button" onclick="AppSorte.assistirAnuncio(aposDesbloqueioAnuncio)" class="px-3.5 py-2.5 rounded-xl font-bold text-xs bg-gradient-to-r from-amber-500/20 to-yellow-500/20 text-amber-300 hover:from-amber-500/30 hover:to-yellow-500/30 border border-amber-500/40 transition shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-tv text-amber-400"></i>
                    <span>Ver Anúncio (+25 Moedas)</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grid: Calculadora e Histórico -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Bloco da Calculadora (Mobile First - 7 colunas em telas grandes) -->
        <div class="lg:col-span-7">
            
            <!-- Banner da Operação Atual Sorteada -->
            <div id="banner-op-atual" class="mb-4 p-3.5 rounded-2xl border transition-all duration-300 flex items-center justify-between
                <?= ($user['is_premium'] || $user['anuncio_ativo']) 
                    ? 'bg-gradient-to-r from-amber-950/40 via-yellow-950/30 to-slate-900 border-amber-500/40' 
                    : 'bg-indigo-950/40 border-indigo-500/30' ?>">
                <div class="flex items-center gap-3">
                    <div id="badge-op-atual" class="w-10 h-10 rounded-xl flex items-center justify-center font-mono font-black text-xl shadow-md
                        <?= ($user['is_premium'] || $user['anuncio_ativo']) 
                            ? 'bg-amber-500 text-slate-950' 
                            : 'bg-indigo-600 text-white' ?>">
                        <?= htmlspecialchars($user['operacao_atual']) ?>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <?= ($user['is_premium'] || $user['anuncio_ativo']) ? 'Status das Operações' : 'Sua Operação Sorteada' ?>
                        </div>
                        <div id="texto-op-atual" class="text-sm font-bold text-white">
                            <?php if ($user['is_premium']): ?>
                                <span class="text-amber-400">VIP Premium: Todas as 4 operações liberadas!</span>
                            <?php elseif ($user['anuncio_ativo']): ?>
                                <span class="text-emerald-400">Bônus de Anúncio: Todas liberadas!</span>
                            <?php else: ?>
                                Operação Permitida: <span class="text-indigo-400 text-base font-mono font-black"><?= $user['operacao_atual'] ?></span> (<?= 
                                    $user['operacao_atual'] === '+' ? 'Adição' : (
                                    $user['operacao_atual'] === '-' ? 'Subtração' : (
                                    $user['operacao_atual'] === '*' ? 'Multiplicação' : 'Divisão'))
                                ?>)
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="sortearOperacao()" class="text-xs font-semibold text-slate-400 hover:text-white px-2 py-1 rounded-lg bg-slate-800/60 border border-slate-700/60 transition" title="Girar roleta de operações">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>

            <!-- Corpo da Calculadora -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-2xl backdrop-blur-xl relative">
                
                <!-- Visor / Display -->
                <div class="bg-slate-950/90 border border-slate-800 rounded-2xl p-4 sm:p-5 mb-5 text-right relative overflow-hidden">
                    <div class="h-6 text-xs font-mono text-slate-400 truncate tracking-wider" id="calc-history">
                        &nbsp;
                    </div>
                    <div class="text-3xl sm:text-4xl font-mono font-bold text-white tracking-tight truncate select-all" id="calc-display">
                        0
                    </div>
                    <div id="reward-badge" class="absolute left-4 top-4 hidden px-2 py-0.5 rounded text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 animate-bounce">
                        +2 Moedas!
                    </div>
                </div>

                <!-- Teclado de Botões da Calculadora -->
                <div class="grid grid-cols-4 gap-2.5 sm:gap-3">
                    
                    <!-- Linha 1 -->
                    <button type="button" onclick="calcClear()" class="py-3.5 sm:py-4 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-bold text-base sm:text-lg border border-rose-500/30 transition transform active:scale-95">
                        C
                    </button>
                    <button type="button" onclick="calcBackspace()" class="py-3.5 sm:py-4 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-base sm:text-lg border border-slate-700/80 transition transform active:scale-95">
                        <i class="fa-solid fa-delete-left"></i>
                    </button>
                    <button type="button" onclick="calcDiceLucky()" class="py-3.5 sm:py-4 rounded-2xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 font-bold text-base sm:text-lg border border-purple-500/30 transition transform active:scale-95" title="Número da Sorte Aleatório">
                        <i class="fa-solid fa-dice text-sm"></i>
                    </button>
                    <!-- Operador Divisão -->
                    <button type="button" data-op="/" onclick="calcOperator('/')" class="op-btn py-3.5 sm:py-4 rounded-2xl font-mono font-black text-xl transition transform active:scale-95 relative flex items-center justify-center">
                        <span>÷</span>
                        <span class="op-lock-icon hidden absolute top-1.5 right-2 text-[10px]"><i class="fa-solid fa-lock"></i></span>
                    </button>

                    <!-- Linha 2 -->
                    <button type="button" onclick="calcDigit('7')" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        7
                    </button>
                    <button type="button" onclick="calcDigit('8')" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        8
                    </button>
                    <button type="button" onclick="calcDigit('9')" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        9
                    </button>
                    <!-- Operador Multiplicação -->
                    <button type="button" data-op="*" onclick="calcOperator('*')" class="op-btn py-3.5 sm:py-4 rounded-2xl font-mono font-black text-xl transition transform active:scale-95 relative flex items-center justify-center">
                        <span>×</span>
                        <span class="op-lock-icon hidden absolute top-1.5 right-2 text-[10px]"><i class="fa-solid fa-lock"></i></span>
                    </button>

                    <!-- Linha 3 -->
                    <button type="button" onclick="calcDigit('4')" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        4
                    </button>
                    <button type="button" onclick="calcDigit('5')" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        5
                    </button>
                    <button type="button" onclick="calcDigit('6')" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        6
                    </button>
                    <!-- Operador Subtração -->
                    <button type="button" data-op="-" onclick="calcOperator('-')" class="op-btn py-3.5 sm:py-4 rounded-2xl font-mono font-black text-xl transition transform active:scale-95 relative flex items-center justify-center">
                        <span>−</span>
                        <span class="op-lock-icon hidden absolute top-1.5 right-2 text-[10px]"><i class="fa-solid fa-lock"></i></span>
                    </button>

                    <!-- Linha 4 -->
                    <button type="button" onclick="calcDigit('1')" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        1
                    </button>
                    <button type="button" onclick="calcDigit('2')" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        2
                    </button>
                    <button type="button" onclick="calcDigit('3')" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        3
                    </button>
                    <!-- Operador Adição -->
                    <button type="button" data-op="+" onclick="calcOperator('+')" class="op-btn py-3.5 sm:py-4 rounded-2xl font-mono font-black text-xl transition transform active:scale-95 relative flex items-center justify-center">
                        <span>+</span>
                        <span class="op-lock-icon hidden absolute top-1.5 right-2 text-[10px]"><i class="fa-solid fa-lock"></i></span>
                    </button>

                    <!-- Linha 5 -->
                    <button type="button" onclick="calcDigit('0')" class="num-btn col-span-2 py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        0
                    </button>
                    <button type="button" onclick="calcDecimal()" class="num-btn py-3.5 sm:py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono font-bold text-lg sm:text-xl border border-slate-700/60 transition transform active:scale-95">
                        .
                    </button>
                    <!-- Botão Igual -->
                    <button type="button" onclick="calcEquals()" class="py-3.5 sm:py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-mono font-black text-xl sm:text-2xl shadow-lg shadow-emerald-600/30 transition transform active:scale-95">
                        =
                    </button>

                </div>

            </div>

        </div>

        <!-- Bloco Lateral: Histórico Recente e Desbloqueios (5 colunas em telas grandes) -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Card de Regras e Sorte -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 sm:p-5">
                <div class="flex items-center gap-2 mb-3 text-sm font-bold text-white">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-400"></i>
                    <span>Como Funciona a Sorte?</span>
                </div>
                <div class="space-y-2 text-xs text-slate-300">
                    <p class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-400 mt-0.5"></i>
                        <span>Os <strong>números (0 a 9)</strong> funcionam normalmente como em qualquer calculadora.</span>
                    </p>
                    <p class="flex items-start gap-2">
                        <i class="fa-solid fa-dice text-indigo-400 mt-0.5"></i>
                        <span>Apenas a <strong>operação sorteada</strong> pode ser executada por vez no plano gratuito.</span>
                    </p>
                    <p class="flex items-start gap-2">
                        <i class="fa-solid fa-unlock text-amber-400 mt-0.5"></i>
                        <span>Desbloqueie todas assistindo a um <strong>anúncio rápido</strong> ou assinando o <strong>VIP Premium</strong>!</span>
                    </p>
                    <p class="flex items-start gap-2">
                        <i class="fa-solid fa-coins text-amber-400 mt-0.5"></i>
                        <span>Cada cálculo feito com sucesso premia você com <strong>+2 moedas</strong> para sua conta.</span>
                    </p>
                </div>
            </div>

            <!-- Card de Histórico Recente de Cálculos -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2 text-sm font-bold text-white">
                        <i class="fa-solid fa-clock-rotate-left text-purple-400"></i>
                        <span>Cálculos Recentes</span>
                    </div>
                    <a href="historico.php" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition">
                        Ver Auditoria →
                    </a>
                </div>

                <div id="recent-calcs-list" class="space-y-2">
                    <?php if (empty($ultimosCalculos)): ?>
                        <div class="text-center py-6 text-xs text-slate-500">
                            Nenhum cálculo registrado ainda. Digite números e teste sua sorte!
                        </div>
                    <?php else: ?>
                        <?php foreach ($ultimosCalculos as $calc): ?>
                            <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-mono font-bold text-white"><?= htmlspecialchars($calc['expressao']) ?></span>
                                    <span class="text-slate-400 mx-1">=</span>
                                    <span class="font-mono font-bold text-emerald-400"><?= htmlspecialchars((string)floatval($calc['resultado'])) ?></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase
                                        <?= $calc['tipo_desbloqueio'] === 'premium' ? 'bg-amber-500/20 text-amber-300' : (
                                            $calc['tipo_desbloqueio'] === 'anuncio' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-indigo-500/20 text-indigo-300'
                                        ) ?>">
                                        <?= htmlspecialchars($calc['tipo_desbloqueio']) ?>
                                    </span>
                                    <span class="text-[10px] text-slate-500"><?= date('H:i', strtotime($calc['created_at'])) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card de Promoção VIP se não for premium -->
            <?php if (!$user['is_premium']): ?>
                <div class="bg-gradient-to-br from-amber-950/40 via-yellow-950/20 to-slate-900 border border-amber-500/30 rounded-2xl p-4 sm:p-5 relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400">
                            <i class="fa-solid fa-crown text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Seja VIP Premium</h3>
                            <p class="text-[11px] text-amber-300/80">Livre-se de qualquer restrição de sorte</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-300 mb-3">Tenha adição, subtração, divisão e multiplicação 100% liberadas permanentemente.</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="AppSorte.solicitarPremium('moedas')" class="py-2 px-3 rounded-xl text-xs font-bold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition">
                            150 Moedas
                        </button>
                        <button type="button" onclick="AppSorte.solicitarPremium('instantaneo')" class="py-2 px-3 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 transition">
                            Ativar Grátis (Demo)
                        </button>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<!-- Variáveis de Estado da Calculadora injetadas com segurança -->
<script>
const UserState = {
    operacaoAtual: "<?= $user['operacao_atual'] ?>",
    isPremium: <?= $user['is_premium'] ? 'true' : 'false' ?>,
    anuncioAtivo: <?= $user['anuncio_ativo'] ? 'true' : 'false' ?>
};

// Gerenciador de Operações e Estilos dos Botões
function atualizarBotoesOperacao() {
    const botoes = document.querySelectorAll('.op-btn');
    botoes.forEach(btn => {
        const op = btn.getAttribute('data-op');
        const lockIcon = btn.querySelector('.op-lock-icon');

        const podeUsar = UserState.isPremium || UserState.anuncioAtivo || (op === UserState.operacaoAtual);

        if (UserState.isPremium) {
            // Estilo VIP Dourado
            btn.className = 'op-btn py-3.5 sm:py-4 rounded-2xl font-mono font-black text-xl transition transform active:scale-95 relative flex items-center justify-center bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500/30 shadow-md shadow-amber-500/10';
            lockIcon.classList.add('hidden');
        } else if (UserState.anuncioAtivo) {
            // Estilo Anúncio Ativo
            btn.className = 'op-btn py-3.5 sm:py-4 rounded-2xl font-mono font-black text-xl transition transform active:scale-95 relative flex items-center justify-center bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500/30 shadow-md shadow-emerald-500/10';
            lockIcon.classList.add('hidden');
        } else if (op === UserState.operacaoAtual) {
            // Operação sorteada ativa!
            btn.className = 'op-btn py-3.5 sm:py-4 rounded-2xl font-mono font-black text-xl transition transform active:scale-95 relative flex items-center justify-center bg-indigo-600 text-white border border-indigo-400 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 ring-2 ring-indigo-400/50';
            lockIcon.classList.add('hidden');
        } else {
            // Operação bloqueada
            btn.className = 'op-btn py-3.5 sm:py-4 rounded-2xl font-mono font-black text-xl transition transform active:scale-95 relative flex items-center justify-center bg-slate-900/60 text-slate-500 border border-slate-800/80 hover:bg-slate-800/60 hover:text-slate-400 opacity-80';
            lockIcon.classList.remove('hidden');
        }
    });
}

// Callback após assistir anúncio da sorte
function aposDesbloqueioAnuncio(data) {
    UserState.anuncioAtivo = true;
    atualizarBotoesOperacao();
    const texto = document.getElementById('texto-op-atual');
    if (texto) {
        texto.innerHTML = '<span class="text-emerald-400">Bônus de Anúncio: Todas as 4 operações liberadas!</span>';
    }
}

// Sorteia nova operação da sorte via AJAX
async function sortearOperacao() {
    const btn = document.getElementById('btn-spin-op');
    const icon = document.getElementById('icon-spin');
    icon.classList.add('fa-spin');
    btn.disabled = true;

    try {
        const response = await fetch('actions/calc_spin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        });
        const data = await response.json();

        if (data.success) {
            UserState.operacaoAtual = data.operacao;
            document.getElementById('badge-op-atual').textContent = data.operacao;
            
            const texto = document.getElementById('texto-op-atual');
            if (!UserState.isPremium && !UserState.anuncioAtivo) {
                texto.innerHTML = `Operação Permitida: <span class="text-indigo-400 text-base font-mono font-black">${data.operacao}</span> (${data.nome})`;
            }

            atualizarBotoesOperacao();
            showToast('info', data.message);
        } else {
            showToast('error', data.message);
        }
    } catch (e) {
        console.error(e);
        showToast('error', 'Erro ao sortear operação.');
    } finally {
        icon.classList.remove('fa-spin');
        btn.disabled = false;
    }
}

/**
 * Lógica Interna da Calculadora
 */
let displayValue = '0';
let firstOperand = null;
let currentOperator = null;
let waitingForSecondOperand = false;

function updateDisplay() {
    const display = document.getElementById('calc-display');
    const history = document.getElementById('calc-history');

    display.textContent = displayValue;

    if (firstOperand !== null && currentOperator !== null) {
        const opSymbol = currentOperator === '*' ? '×' : (currentOperator === '/' ? '÷' : currentOperator);
        history.textContent = `${firstOperand} ${opSymbol} ${waitingForSecondOperand ? '' : displayValue}`;
    } else {
        history.innerHTML = '&nbsp;';
    }
}

function calcDigit(digit) {
    if (waitingForSecondOperand) {
        displayValue = digit;
        waitingForSecondOperand = false;
    } else {
        displayValue = displayValue === '0' ? digit : displayValue + digit;
    }
    updateDisplay();
}

function calcDecimal() {
    if (waitingForSecondOperand) {
        displayValue = '0.';
        waitingForSecondOperand = false;
        updateDisplay();
        return;
    }
    if (!displayValue.includes('.')) {
        displayValue += '.';
        updateDisplay();
    }
}

function calcClear() {
    displayValue = '0';
    firstOperand = null;
    currentOperator = null;
    waitingForSecondOperand = false;
    updateDisplay();
}

function calcBackspace() {
    if (waitingForSecondOperand) return;
    if (displayValue.length > 1) {
        displayValue = displayValue.slice(0, -1);
    } else {
        displayValue = '0';
    }
    updateDisplay();
}

function calcDiceLucky() {
    // Gera um número da sorte aleatório entre 1 e 100
    const luckyNum = Math.floor(Math.random() * 100) + 1;
    displayValue = String(luckyNum);
    waitingForSecondOperand = false;
    updateDisplay();
    showToast('info', `Número da Sorte sorteado: ${luckyNum}!`);
}

function calcOperator(op) {
    const podeUsar = UserState.isPremium || UserState.anuncioAtivo || (op === UserState.operacaoAtual);

    if (!podeUsar) {
        // Bloqueada! Abre MODAL customizado conforme regras de negócio
        const nomes = {'+': 'Adição', '-': 'Subtração', '*': 'Multiplicação', '/': 'Divisão'};
        const msg = `
            <div class="space-y-3">
                <p>A operação <strong>${nomes[op]} (${op})</strong> está temporariamente bloqueada pela sorte!</p>
                <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-xs">
                    Sua operação sorteada atualmente é: <strong class="text-indigo-400 font-mono text-sm">${UserState.operacaoAtual}</strong>
                </div>
                <p class="text-xs text-slate-400">Você pode desbloquear todas as operações das seguintes formas:</p>
                <div class="grid grid-cols-1 gap-2 pt-1">
                    <button type="button" onclick="CustomModal.close(); AppSorte.assistirAnuncio(aposDesbloqueioAnuncio);" class="py-2 px-3 rounded-xl bg-gradient-to-r from-amber-500/20 to-yellow-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold hover:bg-amber-500/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-tv"></i> Assistir Anúncio de 5s (+25 Moedas)
                    </button>
                    <button type="button" onclick="CustomModal.close(); sortearOperacao();" class="py-2 px-3 rounded-xl bg-slate-800 text-slate-200 border border-slate-700 text-xs font-bold hover:bg-slate-700 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-shuffle"></i> Sortear Outra Operação
                    </button>
                </div>
            </div>
        `;

        CustomModal.open({
            title: 'Operação Bloqueada!',
            subtitle: 'Regra da Sorte Matemática',
            message: msg,
            type: 'warning',
            confirmText: 'Entendido',
            showCancel: false
        });
        return;
    }

    const inputValue = parseFloat(displayValue);

    if (firstOperand === null && !isNaN(inputValue)) {
        firstOperand = inputValue;
    } else if (currentOperator) {
        calcEquals();
        firstOperand = parseFloat(displayValue);
    }

    waitingForSecondOperand = true;
    currentOperator = op;
    updateDisplay();
}

async function calcEquals() {
    if (firstOperand === null || currentOperator === null) {
        return;
    }

    const secondOperand = parseFloat(displayValue);
    if (isNaN(secondOperand)) return;

    if (currentOperator === '/' && secondOperand === 0) {
        alertModal('Indeterminação Matemática: Não é possível dividir um número por zero!', 'Erro de Cálculo', 'error');
        return;
    }

    // Envia para o backend processar, validar permissão e registrar auditoria via PDO
    try {
        const payload = {
            num1: firstOperand,
            operador: currentOperator,
            num2: secondOperand
        };

        const response = await fetch('actions/calc_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success) {
            displayValue = String(data.resultado);
            firstOperand = null;
            currentOperator = null;
            waitingForSecondOperand = false;
            updateDisplay();

            // Mostra recompensa animada
            const reward = document.getElementById('reward-badge');
            if (reward) {
                reward.classList.remove('hidden');
                setTimeout(() => reward.classList.add('hidden'), 2500);
            }

            // Atualiza saldo de moedas no topo
            const navCoins = document.getElementById('nav-coins');
            if (navCoins && data.novo_saldo !== undefined) {
                navCoins.textContent = Number(data.novo_saldo).toLocaleString();
            }

            // Adiciona no histórico recente na página
            adicionarAoHistoricoRecente(data.expressao, data.resultado, data.tipo_desbloqueio);

            showToast('success', data.message);
        } else {
            if (data.blocked) {
                calcOperator(payload.operador);
            } else {
                showToast('error', data.message || 'Erro ao calcular.');
            }
        }
    } catch (e) {
        console.error(e);
        showToast('error', 'Falha ao comunicar com o servidor.');
    }
}

function adicionarAoHistoricoRecente(expressao, resultado, tipo) {
    const list = document.getElementById('recent-calcs-list');
    if (!list) return;

    const item = document.createElement('div');
    item.className = 'p-2.5 rounded-xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between text-xs animate-fadeIn';
    
    let tipoBadge = 'bg-indigo-500/20 text-indigo-300';
    if (tipo === 'premium') tipoBadge = 'bg-amber-500/20 text-amber-300';
    if (tipo === 'anuncio') tipoBadge = 'bg-emerald-500/20 text-emerald-300';

    item.innerHTML = `
        <div>
            <span class="font-mono font-bold text-white">${expressao}</span>
            <span class="text-slate-400 mx-1">=</span>
            <span class="font-mono font-bold text-emerald-400">${resultado}</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase ${tipoBadge}">
                ${tipo}
            </span>
            <span class="text-[10px] text-slate-500">Agora</span>
        </div>
    `;

    // Insere no início
    list.prepend(item);
}

document.addEventListener('DOMContentLoaded', () => {
    atualizarBotoesOperacao();
    updateDisplay();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
