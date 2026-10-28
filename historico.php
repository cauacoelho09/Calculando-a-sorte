<?php
/**
 * Página de Histórico & Logs de Auditoria
 * Registra e audita todas as alterações no sistema
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

// Parâmetros de Filtro
$abaAtiva = $_GET['aba'] ?? 'auditoria'; // 'auditoria', 'calculos', 'jogos'
$filtroAcao = trim($_GET['acao'] ?? '');
$busca = trim($_GET['busca'] ?? '');
$limite = 50;

// 1. Consulta dos Logs de Auditoria
$sqlLogs = "
    SELECT l.id, l.user_id, l.acao, l.descricao, l.ip_address, l.user_agent, l.created_at, u.nome, u.email
    FROM logs l
    LEFT JOIN usuarios u ON u.id = l.user_id
    WHERE 1=1
";
$paramsLogs = [];

if (!empty($filtroAcao)) {
    $sqlLogs .= " AND l.acao = ? ";
    $paramsLogs[] = $filtroAcao;
}

if (!empty($busca)) {
    $sqlLogs .= " AND (l.descricao LIKE ? OR l.ip_address LIKE ? OR u.nome LIKE ?) ";
    $paramsLogs[] = "%{$busca}%";
    $paramsLogs[] = "%{$busca}%";
    $paramsLogs[] = "%{$busca}%";
}

$sqlLogs .= " ORDER BY l.id DESC LIMIT {$limite}";
$stmtLogs = $pdo->prepare($sqlLogs);
$stmtLogs->execute($paramsLogs);
$listaLogs = $stmtLogs->fetchAll();

// 2. Consulta de Histórico de Cálculos
$stmtCalcs = $pdo->prepare("
    SELECT c.id, c.expressao, c.operacao, c.resultado, c.tipo_desbloqueio, c.created_at, u.nome
    FROM calculos c
    JOIN usuarios u ON u.id = c.user_id
    WHERE c.user_id = ?
    ORDER BY c.id DESC
    LIMIT {$limite}
");
$stmtCalcs->execute([$user['id']]);
$listaCalculos = $stmtCalcs->fetchAll();

// 3. Consulta de Histórico de Jogos
$stmtGames = $pdo->prepare("
    SELECT j.id, j.tipo_jogo, j.pontos, j.acertos, j.erros, j.moedas_ganhas, j.created_at
    FROM jogos_historico j
    WHERE j.user_id = ?
    ORDER BY j.id DESC
    LIMIT {$limite}
");
$stmtGames->execute([$user['id']]);
$listaJogos = $stmtGames->fetchAll();

// Métricas de Auditoria
$totalLogsGerais = $pdo->query("SELECT COUNT(*) FROM logs")->fetchColumn();
$totalMeusCalculos = $pdo->query("SELECT COUNT(*) FROM calculos WHERE user_id = {$user['id']}")->fetchColumn();

// Lista de ações distintas para o select de filtro
$acoesDistintas = $pdo->query("SELECT DISTINCT acao FROM logs ORDER BY acao ASC")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = "Histórico & Auditoria";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
    
    <!-- Topo -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                    <i class="fa-solid fa-shield-halved text-purple-400 mr-1"></i> Auditoria de Segurança
                </span>
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <i class="fa-solid fa-database mr-1"></i> Rastreamento PDO
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">Histórico & Registros de Auditoria</h1>
            <p class="text-xs sm:text-sm text-slate-400">Rastreabilidade completa de todas as alterações e ações realizadas no sistema.</p>
        </div>

        <!-- Seletor de Abas -->
        <div class="flex items-center gap-1 bg-slate-900/80 p-1.5 rounded-2xl border border-slate-800">
            <a href="?aba=auditoria" class="px-3 py-2 rounded-xl text-xs font-bold transition <?= $abaAtiva === 'auditoria' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-white' ?>">
                <i class="fa-solid fa-list-check mr-1"></i> Logs de Auditoria
            </a>
            <a href="?aba=calculos" class="px-3 py-2 rounded-xl text-xs font-bold transition <?= $abaAtiva === 'calculos' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-white' ?>">
                <i class="fa-solid fa-calculator mr-1"></i> Meus Cálculos
            </a>
            <a href="?aba=jogos" class="px-3 py-2 rounded-xl text-xs font-bold transition <?= $abaAtiva === 'jogos' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-white' ?>">
                <i class="fa-solid fa-gamepad mr-1"></i> Meus Jogos
            </a>
        </div>
    </div>

    <!-- Cards de Métricas -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total de Logs</span>
            <span class="text-xl sm:text-2xl font-black text-white font-mono"><?= number_format($totalLogsGerais) ?></span>
            <span class="text-[10px] text-purple-400 block mt-0.5">Auditoria contínua</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Seus Cálculos</span>
            <span class="text-xl sm:text-2xl font-black text-emerald-400 font-mono"><?= number_format($totalMeusCalculos) ?></span>
            <span class="text-[10px] text-slate-400 block mt-0.5">Gravados com sucesso</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Suas Moedas</span>
            <span class="text-xl sm:text-2xl font-black text-amber-400 font-mono"><?= number_format($user['moedas']) ?></span>
            <span class="text-[10px] text-amber-300/80 block mt-0.5">Saldo disponível</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Status do Usuário</span>
            <span class="text-xl sm:text-2xl font-black text-white font-mono"><?= $user['is_premium'] ? 'VIP' : 'Free' ?></span>
            <span class="text-[10px] <?= $user['is_premium'] ? 'text-amber-400' : 'text-slate-400' ?> block mt-0.5">
                <?= $user['is_premium'] ? 'Todas as 4 operações' : 'Sorteio ativo' ?>
            </span>
        </div>
    </div>

    <!-- CONTEÚDO DA ABA 1: LOGS DE AUDITORIA -->
    <?php if ($abaAtiva === 'auditoria'): ?>
        
        <!-- Formulário de Filtro de Auditoria -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <input type="hidden" name="aba" value="auditoria">
                
                <div class="sm:col-span-5">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar por descrição, IP ou usuário..." 
                            class="w-full pl-9 pr-3 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <select name="acao" class="w-full px-3 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Todas as Ações Auditadas</option>
                        <?php foreach ($acoesDistintas as $ac): ?>
                            <option value="<?= htmlspecialchars($ac) ?>" <?= $filtroAcao === $ac ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ac) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition">
                        Filtrar
                    </button>
                    <?php if (!empty($filtroAcao) || !empty($busca)): ?>
                        <a href="?aba=auditoria" class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition" title="Limpar filtros">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabela / Cards de Logs de Auditoria (Mobile-First) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl backdrop-blur-xl">
            <div class="p-4 sm:p-5 border-b border-slate-800 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-sm font-bold text-white">Eventos Registrados em Tempo Real</h2>
                </div>
                <span class="text-xs text-slate-500 font-mono">Exibindo até <?= count($listaLogs) ?> registros</span>
            </div>

            <?php if (empty($listaLogs)): ?>
                <div class="text-center py-12 text-sm text-slate-500">
                    Nenhum log encontrado com os filtros selecionados.
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-800/80">
                    <?php foreach ($listaLogs as $log): ?>
                        <div class="p-4 hover:bg-slate-800/30 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            
                            <!-- Lado Esquerdo: Ação, Usuário e Descrição -->
                            <div class="flex items-start gap-3 flex-1">
                                <!-- Badge da Ação -->
                                <?php
                                    $badgeStyle = 'bg-slate-800 text-slate-300 border-slate-700';
                                    if (str_contains($log['acao'], 'LOGIN')) $badgeStyle = 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30';
                                    if (str_contains($log['acao'], 'CALC')) $badgeStyle = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
                                    if (str_contains($log['acao'], 'ANUNCIO')) $badgeStyle = 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30';
                                    if (str_contains($log['acao'], 'PREMIUM')) $badgeStyle = 'bg-amber-500/20 text-amber-300 border-amber-500/40 font-bold';
                                    if (str_contains($log['acao'], 'SORTEIO')) $badgeStyle = 'bg-purple-500/20 text-purple-300 border-purple-500/30';
                                    if (str_contains($log['acao'], 'FALHA') || str_contains($log['acao'], 'ERRO')) $badgeStyle = 'bg-rose-500/20 text-rose-300 border-rose-500/30';
                                ?>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold uppercase tracking-wider border <?= $badgeStyle ?> flex-shrink-0">
                                    <?= htmlspecialchars($log['acao']) ?>
                                </span>

                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-white">
                                            <?= htmlspecialchars($log['nome'] ?? 'Visitante Anônimo') ?>
                                        </span>
                                        <?php if (!empty($log['email'])): ?>
                                            <span class="text-slate-500 text-[11px]">&lt;<?= htmlspecialchars($log['email']) ?>&gt;</span>
                                        <?php endif; ?>
                                        <span class="text-slate-600">•</span>
                                        <span class="text-slate-400 font-mono text-[11px]">IP: <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></span>
                                    </div>
                                    <p class="text-slate-300 leading-relaxed font-sans">
                                        <?= htmlspecialchars($log['descricao']) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Lado Direito: Timestamp -->
                            <div class="text-right sm:flex-shrink-0">
                                <span class="font-mono text-slate-400 font-semibold block text-[11px]">
                                    <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                </span>
                                <span class="text-[10px] text-slate-600 block truncate max-w-[150px]" title="<?= htmlspecialchars($log['user_agent'] ?? '') ?>">
                                    <?= htmlspecialchars(substr($log['user_agent'] ?? '', 0, 30)) ?>...
                                </span>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <!-- CONTEÚDO DA ABA 2: MEUS CÁLCULOS -->
    <?php elseif ($abaAtiva === 'calculos'): ?>
        
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl backdrop-blur-xl">
            <div class="p-4 sm:p-5 border-b border-slate-800 flex justify-between items-center">
                <h2 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-indigo-400"></i>
                    <span>Histórico Pessoal de Operações da Calculadora</span>
                </h2>
                <span class="text-xs text-slate-500 font-mono">Total: <?= count($listaCalculos) ?></span>
            </div>

            <?php if (empty($listaCalculos)): ?>
                <div class="text-center py-12 text-sm text-slate-500">
                    Você ainda não efetuou cálculos nesta conta. Acesse a <a href="calculadora.php" class="text-indigo-400 underline">Calculadora da Sorte</a>!
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-800/80">
                    <?php foreach ($listaCalculos as $c): ?>
                        <div class="p-4 hover:bg-slate-800/30 transition flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center font-mono font-black text-lg text-indigo-400">
                                    <?= htmlspecialchars($c['operacao']) ?>
                                </div>
                                <div>
                                    <div class="text-sm font-mono font-bold text-white">
                                        <?= htmlspecialchars($c['expressao']) ?> <span class="text-slate-500">=</span> <span class="text-emerald-400 font-black"><?= htmlspecialchars((string)floatval($c['resultado'])) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                            <?= $c['tipo_desbloqueio'] === 'premium' ? 'bg-amber-500/20 text-amber-300' : (
                                                $c['tipo_desbloqueio'] === 'anuncio' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-indigo-500/20 text-indigo-300'
                                            ) ?>">
                                            Desbloqueio: <?= htmlspecialchars($c['tipo_desbloqueio']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right font-mono text-slate-400 text-xs">
                                <?= date('d/m/Y H:i:s', strtotime($c['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <!-- CONTEÚDO DA ABA 3: MEUS JOGOS -->
    <?php elseif ($abaAtiva === 'jogos'): ?>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl backdrop-blur-xl">
            <div class="p-4 sm:p-5 border-b border-slate-800 flex justify-between items-center">
                <h2 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-gamepad text-emerald-400"></i>
                    <span>Histórico de Desafios Matemáticos</span>
                </h2>
                <span class="text-xs text-slate-500 font-mono">Total: <?= count($listaJogos) ?></span>
            </div>

            <?php if (empty($listaJogos)): ?>
                <div class="text-center py-12 text-sm text-slate-500">
                    Nenhuma partida registrada ainda. Experimente a <a href="jogos.php" class="text-emerald-400 underline">Arena de Jogos</a>!
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-800/80">
                    <?php foreach ($listaJogos as $j): ?>
                        <div class="p-4 hover:bg-slate-800/30 transition flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-lg text-emerald-400">
                                    <i class="fa-solid fa-trophy"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">
                                        <?= htmlspecialchars(
                                            $j['tipo_jogo'] === 'desafio_tempo' ? 'Blitz das 4 Operações (45s)' : (
                                            $j['tipo_jogo'] === 'operador_misterioso' ? 'Qual é o Sinal?' : 'Roleta da Fortuna')
                                        ) ?>
                                    </div>
                                    <div class="text-slate-400 text-[11px] flex items-center gap-3 mt-0.5">
                                        <span>Acertos: <strong class="text-white"><?= $j['acertos'] ?></strong></span>
                                        <span>Erros: <strong class="text-rose-400"><?= $j['erros'] ?></strong></span>
                                        <span class="text-amber-400 font-bold">+<?= $j['moedas_ganhas'] ?> moedas</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono font-black text-amber-400 text-sm">
                                    <?= $j['pontos'] ?> pts
                                </div>
                                <div class="font-mono text-slate-500 text-[11px]">
                                    <?= date('d/m/Y H:i', strtotime($j['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
