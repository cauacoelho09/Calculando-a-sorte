<?php
/**
 * Ação de Salvamento de Partida de Jogos Matemáticos
 * Registra pontos, acertos, moedas e auditoria
 * Calculando a Sorte
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/logger.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Faça login para salvar seus pontos no ranking!']);
    exit;
}

$user = getLoggedUserData($pdo);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário inválido.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$tipoJogo = trim($input['tipo_jogo'] ?? 'desafio_tempo');
$pontos = max(0, (int)($input['pontos'] ?? 0));
$acertos = max(0, (int)($input['acertos'] ?? 0));
$erros = max(0, (int)($input['erros'] ?? 0));
$moedasGanhas = max(0, (int)($input['moedas_ganhas'] ?? 0));

$nomesJogos = [
    'desafio_tempo' => 'Desafio Relâmpago das 4 Operações',
    'operador_misterioso' => 'Qual é o Operador?',
    'roleta_fortuna' => 'Roleta da Fortuna Matemática'
];
$nomeJogoFormatado = $nomesJogos[$tipoJogo] ?? 'Minijogo Matemático';

try {
    $pdo->beginTransaction();

    // 1. Insere histórico do jogo
    $stmt = $pdo->prepare("
        INSERT INTO jogos_historico (user_id, tipo_jogo, pontos, acertos, erros, moedas_ganhas, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $user['id'],
        $tipoJogo,
        $pontos,
        $acertos,
        $erros,
        $moedasGanhas
    ]);
    $jogoId = $pdo->lastInsertId();

    // 2. Credita moedas ganhas
    if ($moedasGanhas > 0) {
        $stmtMoedas = $pdo->prepare("UPDATE usuarios SET moedas = moedas + ? WHERE id = ?");
        $stmtMoedas->execute([$moedasGanhas, $user['id']]);
    }

    // 3. Registra auditoria
    registrarLog(
        $pdo,
        'JOGO_MATEMATICO',
        "Partida finalizada em '{$nomeJogoFormatado}': {$pontos} pts, {$acertos} acertos, {$erros} erros. Recompensa: +{$moedasGanhas} moedas.",
        $user['id']
    );

    $pdo->commit();

    // Consulta novo saldo
    $stmtUser = $pdo->prepare("SELECT moedas FROM usuarios WHERE id = ?");
    $stmtUser->execute([$user['id']]);
    $novoSaldo = $stmtUser->fetchColumn();

    echo json_encode([
        'success' => true,
        'jogo_id' => $jogoId,
        'pontos' => $pontos,
        'moedas_ganhas' => $moedasGanhas,
        'novo_saldo' => (int)$novoSaldo,
        'message' => "Partida registrada! Você fez {$pontos} pontos e ganhou +{$moedasGanhas} moedas!"
    ]);
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao salvar jogo: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno ao salvar pontuação.']);
    exit;
}
