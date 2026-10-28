<?php
/**
 * Ação de Login de Usuário
 * Calculando a Sorte
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    setToast('error', 'Informe seu e-mail e sua senha para entrar.');
    header('Location: ../login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nome, email, senha, is_premium FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        // Login bem-sucedido
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['nome'];

        // Registra log de auditoria
        registrarLog($pdo, 'LOGIN', "Usuário '{$user['nome']}' ({$user['email']}) efetuou login com sucesso.", (int)$user['id']);

        setToast('success', "Olá, {$user['nome']}! Que a sorte matemática esteja com você hoje.");
        header('Location: ../calculadora.php');
        exit;
    } else {
        // Falha no login
        registrarLog($pdo, 'LOGIN_FALHA', "Tentativa falha de login com o e-mail: '{$email}'", null);

        setToast('error', 'E-mail ou senha incorretos. Verifique suas credenciais.');
        header('Location: ../login.php');
        exit;
    }

} catch (PDOException $e) {
    error_log("Erro no login: " . $e->getMessage());
    setToast('error', 'Erro interno ao processar autenticação. Tente novamente.');
    header('Location: ../login.php');
    exit;
}
