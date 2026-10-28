<?php
/**
 * Ação de Atualização de Perfil de Usuário
 * Calculando a Sorte
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/logger.php';
require_once __DIR__ . '/../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../perfil.php');
    exit;
}

$user = getLoggedUserData($pdo);
if (!$user) {
    header('Location: ../login.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$senhaAtual = $_POST['senha_atual'] ?? '';
$novaSenha = $_POST['nova_senha'] ?? '';
$confirmarNovaSenha = $_POST['confirmar_nova_senha'] ?? '';

if (empty($nome)) {
    setToast('error', 'O nome não pode ficar em branco.');
    header('Location: ../perfil.php');
    exit;
}

try {
    // Se solicitou troca de senha
    if (!empty($novaSenha)) {
        if (strlen($novaSenha) < 6) {
            setToast('error', 'A nova senha deve ter no mínimo 6 caracteres.');
            header('Location: ../perfil.php');
            exit;
        }

        if ($novaSenha !== $confirmarNovaSenha) {
            setToast('error', 'A confirmação da nova senha não confere.');
            header('Location: ../perfil.php');
            exit;
        }

        // Valida senha atual
        $stmtSenha = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmtSenha->execute([$user['id']]);
        $senhaHashAtual = $stmtSenha->fetchColumn();

        if (!password_verify($senhaAtual, $senhaHashAtual)) {
            setToast('error', 'A senha atual informada está incorreta.');
            header('Location: ../perfil.php');
            exit;
        }

        // Atualiza nome e nova senha com bcrypt
        $novoHash = password_hash($novaSenha, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, senha = ? WHERE id = ?");
        $stmt->execute([$nome, $novoHash, $user['id']]);

        registrarLog($pdo, 'PERFIL_ATUALIZADO', "Usuário atualizou seu nome para '{$nome}' e alterou sua senha.", $user['id']);
        $_SESSION['user_name'] = $nome;

        setToast('success', 'Perfil e senha atualizados com sucesso!');
        header('Location: ../perfil.php');
        exit;
    } else {
        // Apenas nome
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
        $stmt->execute([$nome, $user['id']]);

        registrarLog($pdo, 'PERFIL_ATUALIZADO', "Usuário atualizou seu nome para '{$nome}'.", $user['id']);
        $_SESSION['user_name'] = $nome;

        setToast('success', 'Dados do perfil atualizados com sucesso!');
        header('Location: ../perfil.php');
        exit;
    }

} catch (PDOException $e) {
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    setToast('error', 'Erro interno ao salvar alterações no perfil.');
    header('Location: ../perfil.php');
    exit;
}
