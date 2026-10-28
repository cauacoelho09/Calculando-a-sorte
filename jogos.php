<?php
/**
 * Plataforma de Jogos Matemáticos
 * Desafios com as 4 operações básicas (+, -, *, /)
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

// Melhores pontuações recentes
$stmtRanking = $pdo->prepare("
    SELECT j.id, j.tipo_jogo, j.pontos, j.acertos, j.moedas_ganhas, j.created_at, u.nome 
    FROM jogos_historico j
    JOIN usuarios u ON u.id = j.user_id
    ORDER BY j.pontos DESC, j.id DESC
    LIMIT 5
");
$stmtRanking->execute();
$ranking = $stmtRanking->fetchAll();

$pageTitle = "Jogos Matemáticos da Sorte";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="max-w-6xl mx-auto px-4 py-6 sm:py-8">
    
    <!-- Topo da Seção -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <i class="fa-solid fa-gamepad text-emerald-400 mr-1"></i> Desafios das 4 Operações
                </span>
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    <i class="fa-solid fa-coins mr-1"></i> Ganhe Moedas
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">Arena dos Jogos Matemáticos</h1>
            <p class="text-xs sm:text-sm text-slate-400">Teste sua velocidade mental nas 4 operações básicas e suba no ranking!</p>
        </div>

        <!-- Seletor de Modo de Jogo -->
        <div class="flex items-center gap-2 bg-slate-900/80 p-1.5 rounded-2xl border border-slate-800">
            <button type="button" onclick="trocarJogo('tempo')" id="tab-tempo" class="px-3.5 py-2 rounded-xl text-xs font-bold transition bg-indigo-600 text-white shadow-md">
                <i class="fa-solid fa-stopwatch mr-1.5"></i> Blitz 45s
            </button>
            <button type="button" onclick="trocarJogo('operador')" id="tab-operador" class="px-3.5 py-2 rounded-xl text-xs font-bold transition text-slate-400 hover:text-white hover:bg-slate-800">
                <i class="fa-solid fa-calculator mr-1.5"></i> Qual é o Sinal?
            </button>
            <button type="button" onclick="trocarJogo('roleta')" id="tab-roleta" class="px-3.5 py-2 rounded-xl text-xs font-bold transition text-slate-400 hover:text-white hover:bg-slate-800">
                <i class="fa-solid fa-dice mr-1.5"></i> Roleta Fortuna
            </button>
        </div>
    </div>

    <!-- Container dos Jogos -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Painel Central Interativo do Jogo Ativo (8 colunas) -->
        <div class="lg:col-span-8">
            
            <!-- JOGO 1: BLITZ 45 SEGUNDOS -->
            <div id="game-tempo-card" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden backdrop-blur-xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-indigo-600/30 text-indigo-400 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-bolt"></i>
                            </span>
                            <span>Blitz das 4 Operações</span>
                        </h2>
                        <p class="text-xs text-slate-400">Resolva o máximo de equações antes do tempo zerar!</p>
                    </div>

                    <!-- Placar e Timer -->
                    <div class="flex items-center gap-3">
                        <div class="bg-slate-950 px-3 py-1.5 rounded-xl border border-slate-800 text-center">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Pontos</span>
                            <span id="blitz-score" class="text-base font-black text-amber-400 font-mono">0</span>
                        </div>
                        <div class="bg-slate-950 px-3 py-1.5 rounded-xl border border-slate-800 text-center">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Tempo</span>
                            <span id="blitz-timer" class="text-base font-black text-rose-400 font-mono">45s</span>
                        </div>
                    </div>
                </div>

                <!-- Tela Inicial / Iniciar -->
                <div id="blitz-start-screen" class="text-center py-10 space-y-4">
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-3xl shadow-xl shadow-indigo-600/20">
                        <i class="fa-solid fa-stopwatch"></i>
                    </div>
                    <div class="max-w-md mx-auto">
                        <h3 class="text-xl font-bold text-white">Pronto para o Desafio?</h3>
                        <p class="text-xs text-slate-400 mt-1">Você terá 45 segundos para resolver operações de soma, subtração, multiplicação e divisão.</p>
                    </div>
                    <button type="button" onclick="iniciarBlitz()" class="py-3.5 px-8 rounded-2xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-500 shadow-xl shadow-indigo-600/30 transition transform hover:scale-105 active:scale-95 inline-flex items-center gap-2">
                        <i class="fa-solid fa-play"></i>
                        <span>Começar Agora!</span>
                    </button>
                </div>

                <!-- Arena da Partida Ativa -->
                <div id="blitz-play-screen" class="hidden text-center py-6 space-y-6">
                    <div class="bg-slate-950/90 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg mx-auto shadow-inner">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider block mb-2">Resolva:</span>
                        <div id="blitz-question" class="text-4xl sm:text-5xl font-mono font-black text-white tracking-tight">
                            12 + 8
                        </div>
                    </div>

                    <!-- Opções de Resposta -->
                    <div id="blitz-options" class="grid grid-cols-2 gap-3 max-w-lg mx-auto">
                        <!-- Botões preenchidos via JS -->
                    </div>
                </div>
            </div>

            <!-- JOGO 2: QUAL É O SINAL / OPERADOR MISTERIOSO -->
            <div id="game-operador-card" class="hidden bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden backdrop-blur-xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-purple-600/30 text-purple-400 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-question"></i>
                            </span>
                            <span>Qual é o Sinal?</span>
                        </h2>
                        <p class="text-xs text-slate-400">Descubra qual operador (+, -, ×, ÷) completa a igualdade!</p>
                    </div>

                    <div class="bg-slate-950 px-3 py-1.5 rounded-xl border border-slate-800 text-center">
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Acertos</span>
                        <span id="op-streak" class="text-base font-black text-purple-400 font-mono">0</span>
                    </div>
                </div>

                <div class="text-center py-6 space-y-6">
                    <!-- Equação com sinal misterioso -->
                    <div class="bg-slate-950/90 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg mx-auto shadow-inner flex items-center justify-center gap-3 font-mono font-black text-3xl sm:text-4xl text-white">
                        <span id="op-n1">15</span>
                        <span id="op-slot" class="w-12 h-12 rounded-xl bg-purple-600/20 border-2 border-dashed border-purple-500/60 text-purple-300 flex items-center justify-center text-2xl animate-pulse">?</span>
                        <span id="op-n2">3</span>
                        <span class="text-slate-500">=</span>
                        <span id="op-res" class="text-emerald-400">5</span>
                    </div>

                    <!-- Botões dos 4 Operadores -->
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Escolha a operação correta:</p>
                    <div class="grid grid-cols-4 gap-3 max-w-md mx-auto">
                        <button type="button" onclick="responderOperador('+')" class="py-4 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-mono font-black text-2xl border border-slate-700 transition transform active:scale-95 shadow-lg">
                            +
                        </button>
                        <button type="button" onclick="responderOperador('-')" class="py-4 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-mono font-black text-2xl border border-slate-700 transition transform active:scale-95 shadow-lg">
                            −
                        </button>
                        <button type="button" onclick="responderOperador('*')" class="py-4 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-mono font-black text-2xl border border-slate-700 transition transform active:scale-95 shadow-lg">
                            ×
                        </button>
                        <button type="button" onclick="responderOperador('/')" class="py-4 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-mono font-black text-2xl border border-slate-700 transition transform active:scale-95 shadow-lg">
                            ÷
                        </button>
                    </div>
                </div>
            </div>

            <!-- JOGO 3: ROLETA DA FORTUNA -->
            <div id="game-roleta-card" class="hidden bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden backdrop-blur-xl">
                <div class="text-center mb-6">
                    <h2 class="text-lg font-bold text-white flex items-center justify-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-dice"></i>
                        </span>
                        <span>Roleta da Fortuna Matemática</span>
                    </h2>
                    <p class="text-xs text-slate-400">Gire a roleta para testar sua sorte e faturar até 100 moedas!</p>
                </div>

                <div class="flex flex-col items-center justify-center py-6 space-y-6">
                    <!-- Roda Visual -->
                    <div class="relative w-48 h-48 sm:w-56 sm:h-56">
                        <div id="wheel-disc" class="w-full h-full rounded-full border-4 border-amber-500/40 bg-gradient-to-tr from-amber-600 via-purple-600 to-indigo-600 shadow-2xl flex items-center justify-center p-2 transition-all duration-[3000ms] ease-out">
                            <div class="w-full h-full rounded-full bg-slate-950 flex flex-col items-center justify-center text-center p-3 border border-white/10">
                                <i class="fa-solid fa-gem text-amber-400 text-3xl mb-1"></i>
                                <span id="wheel-result-label" class="text-xs font-bold text-white">Gire a Roleta!</span>
                            </div>
                        </div>
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 text-amber-400 text-xl">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                    </div>

                    <button type="button" id="btn-spin-wheel" onclick="girarRoletaFortuna()" class="py-3.5 px-8 rounded-2xl font-bold text-sm text-slate-950 bg-gradient-to-r from-amber-400 to-yellow-300 hover:from-amber-300 hover:to-yellow-200 shadow-xl shadow-amber-500/30 transition transform hover:scale-105 active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-rotate-right"></i>
                        <span>Girar Roleta da Sorte</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Ranking & Estatísticas (4 colunas) -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Card de Ranking -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 sm:p-5">
                <div class="flex items-center gap-2 mb-3 text-sm font-bold text-white">
                    <i class="fa-solid fa-trophy text-amber-400"></i>
                    <span>Melhores Pontuações</span>
                </div>

                <div class="space-y-2">
                    <?php if (empty($ranking)): ?>
                        <div class="text-center py-6 text-xs text-slate-500">
                            Nenhuma partida registrada ainda. Seja o primeiro a pontuar!
                        </div>
                    <?php else: ?>
                        <?php foreach ($ranking as $i => $row): ?>
                            <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold <?= $i === 0 ? 'bg-amber-500 text-slate-950' : ($i === 1 ? 'bg-slate-300 text-slate-950' : 'bg-slate-800 text-slate-400') ?>">
                                        <?= $i + 1 ?>
                                    </span>
                                    <div>
                                        <span class="font-bold text-white block truncate max-w-[110px]"><?= htmlspecialchars($row['nome']) ?></span>
                                        <span class="text-[10px] text-slate-500"><?= $row['acertos'] ?> acertos</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold text-amber-400"><?= $row['pontos'] ?> pts</span>
                                    <span class="text-[10px] text-slate-500 block">+<?= $row['moedas_ganhas'] ?> moedas</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Dica de Treino -->
            <div class="bg-indigo-950/30 border border-indigo-500/20 rounded-2xl p-4 text-xs text-indigo-200">
                <div class="font-bold text-white mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-lightbulb text-amber-400"></i>
                    <span>Dica de Especialista</span>
                </div>
                <p class="text-slate-400 leading-relaxed">
                    Jogar os minijogos ajuda você a acumular moedas para desbloquear o status VIP Premium sem gastar nada!
                </p>
            </div>

        </div>

    </div>

</div>

<!-- Scripts dos Jogos Matemáticos -->
<script>
function trocarJogo(tipo) {
    document.getElementById('game-tempo-card').classList.add('hidden');
    document.getElementById('game-operador-card').classList.add('hidden');
    document.getElementById('game-roleta-card').classList.add('hidden');

    document.getElementById('tab-tempo').className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition text-slate-400 hover:text-white hover:bg-slate-800';
    document.getElementById('tab-operador').className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition text-slate-400 hover:text-white hover:bg-slate-800';
    document.getElementById('tab-roleta').className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition text-slate-400 hover:text-white hover:bg-slate-800';

    if (tipo === 'tempo') {
        document.getElementById('game-tempo-card').classList.remove('hidden');
        document.getElementById('tab-tempo').className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition bg-indigo-600 text-white shadow-md';
    } else if (tipo === 'operador') {
        document.getElementById('game-operador-card').classList.remove('hidden');
        document.getElementById('tab-operador').className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition bg-purple-600 text-white shadow-md';
        novoDesafioOperador();
    } else {
        document.getElementById('game-roleta-card').classList.remove('hidden');
        document.getElementById('tab-roleta').className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition bg-amber-500 text-slate-950 shadow-md';
    }
}

/**
 * JOGO 1: BLITZ 45 SEGUNDOS
 */
let blitzInterval = null;
let blitzTime = 45;
let blitzScore = 0;
let blitzAcertos = 0;
let blitzErros = 0;
let blitzRespostaAtual = 0;

function iniciarBlitz() {
    blitzTime = 45;
    blitzScore = 0;
    blitzAcertos = 0;
    blitzErros = 0;

    document.getElementById('blitz-score').textContent = '0';
    document.getElementById('blitz-timer').textContent = '45s';

    document.getElementById('blitz-start-screen').classList.add('hidden');
    document.getElementById('blitz-play-screen').classList.remove('hidden');

    gerarPerguntaBlitz();

    clearInterval(blitzInterval);
    blitzInterval = setInterval(() => {
        blitzTime--;
        document.getElementById('blitz-timer').textContent = `${blitzTime}s`;

        if (blitzTime <= 0) {
            clearInterval(blitzInterval);
            finalizarBlitz();
        }
    }, 1000);
}

function gerarPerguntaBlitz() {
    const ops = ['+', '-', '*', '/'];
    const op = ops[Math.floor(Math.random() * ops.length)];
    let n1, n2, resposta, opVisual;

    if (op === '+') {
        n1 = Math.floor(Math.random() * 50) + 1;
        n2 = Math.floor(Math.random() * 50) + 1;
        resposta = n1 + n2;
        opVisual = '+';
    } else if (op === '-') {
        n1 = Math.floor(Math.random() * 50) + 10;
        n2 = Math.floor(Math.random() * n1) + 1;
        resposta = n1 - n2;
        opVisual = '−';
    } else if (op === '*') {
        n1 = Math.floor(Math.random() * 12) + 2;
        n2 = Math.floor(Math.random() * 10) + 2;
        resposta = n1 * n2;
        opVisual = '×';
    } else {
        // Divisão com resultado inteiro exato
        n2 = Math.floor(Math.random() * 9) + 2;
        resposta = Math.floor(Math.random() * 10) + 1;
        n1 = n2 * resposta;
        opVisual = '÷';
    }

    blitzRespostaAtual = resposta;
    document.getElementById('blitz-question').textContent = `${n1} ${opVisual} ${n2}`;

    // Gera 4 opções incluindo a correta
    const opcoes = new Set([resposta]);
    while (opcoes.size < 4) {
        const offset = (Math.floor(Math.random() * 7) + 1) * (Math.random() > 0.5 ? 1 : -1);
        const falsa = Math.max(0, resposta + offset);
        opcoes.add(falsa);
    }

    const container = document.getElementById('blitz-options');
    container.innerHTML = '';

    const arrayOpcoes = Array.from(opcoes).sort(() => Math.random() - 0.5);
    arrayOpcoes.forEach(opt => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'py-4 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-mono font-bold text-xl border border-slate-700 transition transform active:scale-95';
        btn.textContent = opt;
        btn.onclick = () => checarRespostaBlitz(opt);
        container.appendChild(btn);
    });
}

function checarRespostaBlitz(escolha) {
    if (escolha === blitzRespostaAtual) {
        blitzScore += 10;
        blitzAcertos++;
        showToast('success', '+10 Pontos!');
    } else {
        blitzScore = Math.max(0, blitzScore - 5);
        blitzErros++;
        showToast('error', 'Ops, resposta incorreta!');
    }

    document.getElementById('blitz-score').textContent = blitzScore;
    gerarPerguntaBlitz();
}

async function finalizarBlitz() {
    const moedasGanhas = Math.floor(blitzScore / 10);

    // Salva no backend via AJAX
    try {
        const response = await fetch('actions/game_save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tipo_jogo: 'desafio_tempo',
                pontos: blitzScore,
                acertos: blitzAcertos,
                erros: blitzErros,
                moedas_ganhas: moedasGanhas
            })
        });
        const data = await response.json();

        if (data.novo_saldo !== undefined) {
            const navCoins = document.getElementById('nav-coins');
            if (navCoins) navCoins.textContent = Number(data.novo_saldo).toLocaleString();
        }

        CustomModal.open({
            title: 'Tempo Esgotado!',
            subtitle: 'Resultado do Blitz 45s',
            message: `
                <div class="text-center space-y-3">
                    <p class="text-3xl font-black text-amber-400 font-mono">${blitzScore} PONTOS</p>
                    <p class="text-xs text-slate-300">Você acertou <strong>${blitzAcertos}</strong> equações e errou <strong>${blitzErros}</strong>.</p>
                    <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-300 font-bold text-sm">
                        +${moedasGanhas} Moedas da Sorte Creditadas!
                    </div>
                </div>
            `,
            type: 'success',
            confirmText: 'Jogar Novamente',
            onConfirm: () => {
                document.getElementById('blitz-play-screen').classList.add('hidden');
                document.getElementById('blitz-start-screen').classList.remove('hidden');
            }
        });

    } catch (e) {
        console.error(e);
        alertModal(`Partida finalizada! Pontuação: ${blitzScore} pts`, 'Fim de Jogo');
        document.getElementById('blitz-play-screen').classList.add('hidden');
        document.getElementById('blitz-start-screen').classList.remove('hidden');
    }
}

/**
 * JOGO 2: QUAL É O SINAL?
 */
let opOperadorCorreto = '+';
let opAcertosConsecutivos = 0;

function novoDesafioOperador() {
    const ops = ['+', '-', '*', '/'];
    opOperadorCorreto = ops[Math.floor(Math.random() * ops.length)];
    let n1, n2, res;

    if (opOperadorCorreto === '+') {
        n1 = Math.floor(Math.random() * 30) + 1;
        n2 = Math.floor(Math.random() * 30) + 1;
        res = n1 + n2;
    } else if (opOperadorCorreto === '-') {
        n1 = Math.floor(Math.random() * 40) + 10;
        n2 = Math.floor(Math.random() * n1) + 1;
        res = n1 - n2;
    } else if (opOperadorCorreto === '*') {
        n1 = Math.floor(Math.random() * 9) + 2;
        n2 = Math.floor(Math.random() * 9) + 2;
        res = n1 * n2;
    } else {
        n2 = Math.floor(Math.random() * 8) + 2;
        res = Math.floor(Math.random() * 9) + 1;
        n1 = n2 * res;
    }

    document.getElementById('op-n1').textContent = n1;
    document.getElementById('op-n2').textContent = n2;
    document.getElementById('op-res').textContent = res;
    document.getElementById('op-slot').textContent = '?';
}

async function responderOperador(opEscolhido) {
    if (opEscolhido === opOperadorCorreto) {
        opAcertosConsecutivos++;
        document.getElementById('op-streak').textContent = opAcertosConsecutivos;
        showToast('success', 'Correto! Operador perfeito!');

        // A cada 3 acertos seguidos, salva pontuação e dá moedas
        if (opAcertosConsecutivos % 3 === 0) {
            await fetch('actions/game_save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    tipo_jogo: 'operador_misterioso',
                    pontos: opAcertosConsecutivos * 10,
                    acertos: opAcertosConsecutivos,
                    erros: 0,
                    moedas_ganhas: 5
                })
            }).then(r => r.json()).then(d => {
                if (d.novo_saldo) {
                    document.getElementById('nav-coins').textContent = Number(d.novo_saldo).toLocaleString();
                }
            });
            showToast('info', 'Combo de 3 acertos: +5 moedas ganhas!');
        }

        novoDesafioOperador();
    } else {
        alertModal(`Não foi dessa vez! O operador correto era "${opOperadorCorreto}". Tente novamente!`, 'Ops!', 'error');
        opAcertosConsecutivos = 0;
        document.getElementById('op-streak').textContent = '0';
        novoDesafioOperador();
    }
}

/**
 * JOGO 3: ROLETA DA FORTUNA
 */
let roletaGirando = false;
async function girarRoletaFortuna() {
    if (roletaGirando) return;
    roletaGirando = true;

    const disc = document.getElementById('wheel-disc');
    const label = document.getElementById('wheel-result-label');
    const btn = document.getElementById('btn-spin-wheel');

    btn.disabled = true;
    label.textContent = 'Girando a sorte...';

    const grausAleatorios = Math.floor(Math.random() * 360) + 1440; // no mínimo 4 voltas
    disc.style.transform = `rotate(${grausAleatorios}deg)`;

    setTimeout(async () => {
        roletaGirando = false;
        btn.disabled = false;

        const premios = [10, 20, 30, 50, 15, 25];
        const premioSorteado = premios[Math.floor(Math.random() * premios.length)];

        label.textContent = `+${premioSorteado} Moedas!`;

        // Salva recompensa
        try {
            const resp = await fetch('actions/game_save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    tipo_jogo: 'roleta_fortuna',
                    pontos: premioSorteado * 5,
                    acertos: 1,
                    erros: 0,
                    moedas_ganhas: premioSorteado
                })
            });
            const data = await resp.json();

            if (data.novo_saldo) {
                document.getElementById('nav-coins').textContent = Number(data.novo_saldo).toLocaleString();
            }

            showToast('success', `🎉 Roleta premiada: você ganhou +${premioSorteado} moedas da sorte!`);
        } catch (e) {
            console.error(e);
        }
    }, 3100);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
