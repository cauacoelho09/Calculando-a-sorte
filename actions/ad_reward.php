<?php
/**
 * Ação de Recompensa por Assistir Anúncio
 * Desbloqueia temporariamente todas as 4 operações básicas
 * Calculando a Sorte
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/logger.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Faça login para receber a recompensa do anúncio!']);
    exit;
}

$user = getLoggedUserData($pdo);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário inválido.']);
    exit;
}

try {
    $minutos = TEMPO_DESBLOQUEIO_ANUNCIO_MINUTOS;
    $moedasBonus = MOEDAS_PREMIO_ANUNCIO;

    // Atualiza o tempo de desbloqueio temporário e adiciona moedas
    $stmt = $pdo->prepare("
        UPDATE usuarios 
        SET anuncio_desbloqueio_ate = DATE_ADD(NOW(), INTERVAL ? MINUTE),
            moedas = moedas + ?
        WHERE id = ?
    ");
    $stmt->execute([$minutos, $moedasBonus, $user['id']]);

    // Registra log de auditoria
    registrarLog(
        $pdo, 
        'ANUNCIO_ASSISTIDO', 
        "Usuário assistiu anúncio promocional. Recompensa: todas as operações desbloqueadas por {$minutos} minutos e +{$moedasBonus} moedas.", 
        $user['id']
    );

    // Consulta novo saldo
    $stmtUser = $pdo->prepare("SELECT moedas, anuncio_desbloqueio_ate FROM usuarios WHERE id = ?");
    $stmtUser->execute([$user['id']]);
    $updated = $stmtUser->fetch();

    echo json_encode([
        'success' => true,
        'minutos' => $minutos,
        'moedas_ganhas' => $moedasBonus,
        'novo_saldo' => (int)$updated['moedas'],
        'expira_em' => $updated['anuncio_desbloqueio_ate'],
        'message' => "Parabéns! Todas as 4 operações (+, -, *, /) foram liberadas por {$minutos} minutos e você ganhou +{$moedasBonus} moedas!"
    ]);
    exit;

} catch (PDOException $e) {
    error_log("Erro ao processar anúncio: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno ao aplicar recompensa do anúncio.']);
    exit;
}
