<?php
/**
 * Sistema Central de Logs e Auditoria
 * Registra todas as ações e alterações realizadas por usuários
 * Calculando a Sorte
 */

/**
 * Registra uma entrada de log na tabela de auditoria
 * 
 * @param PDO $pdo Instância da conexão PDO
 * @param string $acao Tipo/Nome da ação (ex: 'LOGIN', 'CADASTRO', 'CALCULO', 'SORTEIO_OP', 'DESBLOQUEIO_ANUNCIO', 'PREMIUM_UPGRADE')
 * @param string $descricao Detalhes específicos da ação realizada
 * @param int|null $userId ID do usuário (se omitido, busca da sessão ativa)
 * @return bool Retorna verdadeiro se o log foi gravado com sucesso
 */
function registrarLog(PDO $pdo, string $acao, string $descricao, ?int $userId = null): bool {
    try {
        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = (int)$_SESSION['user_id'];
        }

        // Obtém IP real do cliente
        $ip = '127.0.0.1';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido', 0, 250);

        $stmt = $pdo->prepare("
            INSERT INTO logs (user_id, acao, descricao, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        return $stmt->execute([
            $userId,
            strtoupper(trim($acao)),
            $descricao,
            $ip,
            $userAgent
        ]);
    } catch (Exception $e) {
        // Falha no log não deve derrubar o sistema, mas pode ser gravada em log do PHP
        error_log("Erro ao registrar log de auditoria: " . $e->getMessage());
        return false;
    }
}
