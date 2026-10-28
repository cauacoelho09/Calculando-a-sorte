<?php
/**
 * Ação de Cadastro de Novo Usuário
 * Calculando a Sorte
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmarSenha = $_POST['confirmar_senha'] ?? '';

// Validações
if (empty($nome) || empty($email) || empty($senha)) {
    setToast('error', 'Por favor, preencha todos os campos obrigatórios.');
    header('Location: ../register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setToast('error', 'O endereço de e-mail informado é inválido.');
    header('Location: ../register.php');
    exit;
}

if (strlen($senha) < 6) {
    setToast('error', 'A senha deve conter no mínimo 6 caracteres.');
    header('Location: ../register.php');
    exit;
}

if ($senha !== $confirmarSenha) {
    setToast('error', 'As senhas informadas não coincidem.');
    header('Location: ../register.php');
    exit;
}

try {
    // Verifica se e-mail já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        setToast('error', 'Este e-mail já está cadastrado na plataforma.');
        header('Location: ../register.php');
        exit;
    }

    // Hash bcrypt da senha conforme regra CORE
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    // Sorteia uma operação inicial (+, -, *, /)
    $operacoes = ['+', '-', '*', '/'];
    $operacaoInicial = $operacoes[array_rand($operacoes)];

    // Insere o novo usuário
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nome, email, senha, is_premium, moedas, operacao_atual, operacoes_desbloqueadas, created_at)
        VALUES (?, ?, ?, 0, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $nome,
        $email,
        $senhaHash,
        MOEDAS_INICIAIS,
        $operacaoInicial,
        $operacaoInicial
    ]);

    $novoUserId = (int)$pdo->lastInsertId();

    // Registra log de auditoria
    registrarLog($pdo, 'CADASTRO', "Novo usuário cadastrado: {$nome} ({$email}) com operação inicial '{$operacaoInicial}'", $novoUserId);

    // Autentica o usuário na sessão nativa
    session_regenerate_id(true);
    $_SESSION['user_id'] = $novoUserId;
    $_SESSION['user_name'] = $nome;

    setToast('success', "Bem-vindo ao Calculando a Sorte, {$nome}! Você ganhou " . MOEDAS_INICIAIS . " moedas e sua operação da sorte inicial é '{$operacaoInicial}'.");
    header('Location: ../calculadora.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro no cadastro: " . $e->getMessage());
    setToast('error', 'Ocorreu um erro interno ao criar sua conta. Tente novamente mais tarde.');
    header('Location: ../register.php');
    exit;
}
