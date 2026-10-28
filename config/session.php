<?php
/**
 * Gerenciamento Seguro de Sessões Nativas
 * Calculando a Sorte
 */

if (session_status() === PHP_SESSION_NONE) {
    // Configurações de cookies de sessão seguros
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');

    session_start();
}

/**
 * Verifica se há um usuário autenticado na sessão
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Retorna os dados do usuário autenticado a partir do banco de dados para garantir dados frescos
 */
function getLoggedUserData(PDO $pdo): ?array {
    if (!isLoggedIn()) {
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, is_premium, moedas, operacao_atual, operacoes_desbloqueadas, anuncio_desbloqueio_ate, created_at FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user) {
            // Usuário não existe mais no banco
            unset($_SESSION['user_id']);
            unset($_SESSION['user_name']);
            return null;
        }

        // Verifica se o desbloqueio temporário por anúncio expirou
        if (!empty($user['anuncio_desbloqueio_ate'])) {
            $agora = new DateTime();
            $expira = new DateTime($user['anuncio_desbloqueio_ate']);
            $user['anuncio_ativo'] = ($agora < $expira);
        } else {
            $user['anuncio_ativo'] = false;
        }

        return $user;
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Define uma mensagem Flash (Toast) para ser exibida na próxima requisição
 * @param string $tipo 'success', 'error', 'warning', 'info'
 * @param string $mensagem Texto da mensagem
 */
function setToast(string $tipo, string $mensagem): void {
    $_SESSION['toast'] = [
        'tipo' => $tipo,
        'mensagem' => $mensagem
    ];
}

/**
 * Recupera e consome a mensagem Flash (Toast)
 */
function getToast(): ?array {
    if (isset($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        unset($_SESSION['toast']);
        return $toast;
    }
    return null;
}
