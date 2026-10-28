<?php
/**
 * Ação de Sorteio de Operação da Sorte
 * Sorteia aleatoriamente entre +, -, *, /
 * Calculando a Sorte
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/logger.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Faça login para girar a roleta da sorte!']);
    exit;
}

$user = getLoggedUserData($pdo);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
    exit;
}

$operacoes = ['+', '-', '*', '/'];
$nomesOperacoes = [
    '+' => 'Adição (+)',
    '-' => 'Subtração (-)',
    '*' => 'Multiplicação (×)',
    '/' => 'Divisão (÷)'
];

// Sorteia uma operação (diferente da atual se possível para dar dinâmica)
$possiveis = array_values(array_filter($operacoes, fn($op) => $op !== $user['operacao_atual']));
if (empty($possiveis)) {
    $possiveis = $operacoes;
}
$novaOperacao = $possiveis[array_rand($possiveis)];

try {
    $stmt = $pdo->prepare("UPDATE usuarios SET operacao_atual = ? WHERE id = ?");
    $stmt->execute([$novaOperacao, $user['id']]);

    // Registra auditoria
    registrarLog(
        $pdo, 
        'SORTEIO_OPERACAO', 
        "Usuário sorteou a operação '{$novaOperacao}' ({$nomesOperacoes[$novaOperacao]}) para a calculadora.", 
        $user['id']
    );

    echo json_encode([
        'success' => true,
        'operacao' => $novaOperacao,
        'nome' => $nomesOperacoes[$novaOperacao],
        'is_premium' => (bool)$user['is_premium'],
        'anuncio_ativo' => (bool)$user['anuncio_ativo'],
        'message' => "Operação sorteada com sucesso: {$nomesOperacoes[$novaOperacao]}!"
    ]);
    exit;

} catch (PDOException $e) {
    error_log("Erro ao sortear operação: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno ao realizar sorteio.']);
    exit;
}
