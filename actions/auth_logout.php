<?php
/**
 * Ação de Logout do Usuário
 * Calculando a Sorte
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/logger.php';

if (isLoggedIn()) {
    $userId = (int)$_SESSION['user_id'];
    $userName = $_SESSION['user_name'] ?? 'Usuário';

    // Registra log de auditoria
    registrarLog($pdo, 'LOGOUT', "Usuário '{$userName}' encerrou a sessão com sucesso.", $userId);

    // Limpa variáveis de sessão
    $_SESSION = [];

    // Se desejar destruir o cookie de sessão também
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    // Reinicia para setar toast na nova sessão
    session_start();
    setToast('info', 'Você saiu da sua conta com segurança. Até breve!');
}

header('Location: ../login.php');
exit;
