<?php
/**
 * Ação de Processamento de Cálculo Matemático
 * Valida permissão da operação sorteada / premium / anúncio
 * Salva no banco de dados e gera log de auditoria
 * Calculando a Sorte
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/logger.php';

if (!isLoggedIn()) {
    echo json_encode([
        'success' => false, 
        'message' => 'Faça login para realizar cálculos e salvar seu histórico!'
    ]);
    exit;
}

$user = getLoggedUserData($pdo);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário inválido.']);
    exit;
}

// Recebe dados via JSON ou POST
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$num1 = isset($input['num1']) ? filter_var($input['num1'], FILTER_VALIDATE_FLOAT) : false;
$operador = trim($input['operador'] ?? '');
$num2 = isset($input['num2']) ? filter_var($input['num2'], FILTER_VALIDATE_FLOAT) : false;

if ($num1 === false || $num2 === false || empty($operador)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Parâmetros numéricos ou operador inválidos.'
    ]);
    exit;
}

$operacoesValidas = ['+', '-', '*', '/'];
if (!in_array($operador, $operacoesValidas, true)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Operador matemático não suportado.'
    ]);
    exit;
}

// Verificação de Regra de Negócio: Operação Sorteada vs Premium vs Anúncio
$isPremium = (bool)$user['is_premium'];
$isAnuncioAtivo = (bool)$user['anuncio_ativo'];
$operacaoAtual = $user['operacao_atual'];

$tipoDesbloqueio = 'sorteio';
if ($isPremium) {
    $tipoDesbloqueio = 'premium';
} elseif ($isAnuncioAtivo) {
    $tipoDesbloqueio = 'anuncio';
} else {
    // Modo gratuito: só pode executar a operação que foi sorteada!
    if ($operador !== $operacaoAtual) {
        $nomes = ['+' => 'Adição', '-' => 'Subtração', '*' => 'Multiplicação', '/' => 'Divisão'];
        echo json_encode([
            'success' => false,
            'blocked' => true,
            'message' => "A operação '{$operador}' ({$nomes[$operador]}) está bloqueada pela Sorte! Sua operação sorteada no momento é '{$operacaoAtual}'. Desbloqueie todas assistindo a um anúncio ou sendo Premium!",
            'operacao_atual' => $operacaoAtual
        ]);
        exit;
    }
}

// Execução da operação matemática com segurança
$resultado = null;
switch ($operador) {
    case '+':
        $resultado = $num1 + $num2;
        break;
    case '-':
        $resultado = $num1 - $num2;
        break;
    case '*':
        $resultado = $num1 * $num2;
        break;
    case '/':
        if ($num2 == 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'Indeterminação matemática: Impossível dividir por zero!'
            ]);
            exit;
        }
        $resultado = $num1 / $num2;
        break;
}

$expressao = "{$num1} {$operador} {$num2}";
$resultadoFormatado = round($resultado, 4);

try {
    // Inicia transação
    $pdo->beginTransaction();

    // 1. Salva o cálculo no histórico
    $stmtCalc = $pdo->prepare("
        INSERT INTO calculos (user_id, expressao, operacao, resultado, tipo_desbloqueio, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmtCalc->execute([
        $user['id'],
        $expressao,
        $operador,
        $resultadoFormatado,
        $tipoDesbloqueio
    ]);
    $calcId = $pdo->lastInsertId();

    // 2. Concede moedas da sorte ao usuário por calcular (+2 moedas)
    $moedasGanhas = 2;
    $stmtMoedas = $pdo->prepare("UPDATE usuarios SET moedas = moedas + ? WHERE id = ?");
    $stmtMoedas->execute([$moedasGanhas, $user['id']]);

    // 3. Registra log de auditoria
    registrarLog(
        $pdo, 
        'CALCULO', 
        "Cálculo realizado: '{$expressao} = {$resultadoFormatado}' [Tipo: {$tipoDesbloqueio}]. Recompensa: +{$moedasGanhas} moedas.", 
        $user['id']
    );

    $pdo->commit();

    // Busca saldo atualizado
    $stmtSaldo = $pdo->prepare("SELECT moedas FROM usuarios WHERE id = ?");
    $stmtSaldo->execute([$user['id']]);
    $saldoAtual = $stmtSaldo->fetchColumn();

    echo json_encode([
        'success' => true,
        'calc_id' => $calcId,
        'expressao' => $expressao,
        'operador' => $operador,
        'resultado' => $resultadoFormatado,
        'tipo_desbloqueio' => $tipoDesbloqueio,
        'moedas_ganhas' => $moedasGanhas,
        'novo_saldo' => (int)$saldoAtual,
        'message' => "Resultado: {$resultadoFormatado} (+{$moedasGanhas} moedas da sorte!)"
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao salvar cálculo: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro interno ao processar o cálculo e registrar auditoria.'
    ]);
    exit;
}
