<?php
/**
 * Ação de Upgrade para o Plano Premium
 * Desbloqueia permanentemente todas as 4 operações básicas
 * Calculando a Sorte
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/logger.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Faça login para assinar o plano Premium!']);
    exit;
}

$user = getLoggedUserData($pdo);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário inválido.']);
    exit;
}

if ($user['is_premium']) {
    echo json_encode(['success' => false, 'message' => 'Você já é um membro VIP Premium!']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$metodo = $input['metodo'] ?? 'moedas'; // 'moedas' ou 'instantaneo'

try {
    $pdo->beginTransaction();

    if ($metodo === 'moedas') {
        $preco = MOEDAS_PRECO_PREMIUM;
        if ($user['moedas'] < $preco) {
            $pdo->rollBack();
            echo json_encode([
                'success' => false, 
                'message' => "Saldo insuficiente de moedas. Você possui {$user['moedas']} moedas, mas são necessárias {$preco} moedas. Jogue ou assista anúncios para acumular mais!"
            ]);
            exit;
        }

        // Deduz moedas e ativa premium
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET is_premium = 1,
                moedas = moedas - ?,
                operacoes_desbloqueadas = '+,-,*,/'
            WHERE id = ?
        ");
        $stmt->execute([$preco, $user['id']]);

        $descricao = "Upgrade para VIP Premium realizado com {$preco} moedas da plataforma.";

    } else {
        // Ativação Direta / Modo Demonstração
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET is_premium = 1,
                operacoes_desbloqueadas = '+,-,*,/'
            WHERE id = ?
        ");
        $stmt->execute([$user['id']]);

        $descricao = "Upgrade para VIP Premium ativado em modo VIP direto.";
    }

    // Registra log de auditoria
    registrarLog($pdo, 'UPGRADE_PREMIUM', $descricao, $user['id']);

    $pdo->commit();

    // Consulta dados atualizados
    $stmtUser = $pdo->prepare("SELECT moedas, is_premium FROM usuarios WHERE id = ?");
    $stmtUser->execute([$user['id']]);
    $updated = $stmtUser->fetch();

    echo json_encode([
        'success' => true,
        'is_premium' => true,
        'novo_saldo' => (int)$updated['moedas'],
        'message' => '👑 Parabéns! Você agora é um membro VIP Premium! Todas as operações (+, -, *, /) estão desbloqueadas para sempre!'
    ]);
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro no upgrade premium: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno ao processar upgrade.']);
    exit;
}
