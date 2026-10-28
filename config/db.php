<?php
/**
 * Conexão Segura com Banco de Dados via PDO
 * Calculando a Sorte
 */

require_once __DIR__ . '/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // Se a base de dados não existir ainda, tenta conexão com o servidor MySQL para permitir auto-instalação
    try {
        $dsnServer = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
        $pdoServer = new PDO($dsnServer, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Verifica se a página atual já é a de instalação para não entrar em loop
        $currentPage = basename($_SERVER['PHP_SELF'] ?? '');
        if ($currentPage !== 'instalar.php') {
            header('Location: instalar.php');
            exit;
        }
    } catch (PDOException $exServer) {
        // Falha geral de conexão com o MySQL
        die("
            <div style='font-family:sans-serif; max-width:600px; margin:50px auto; padding:20px; border-radius:12px; background:#fff1f2; border:1px solid #fecdd3; color:#9f1239;'>
                <h2 style='margin-top:0;'>Erro de Conexão com o Banco de Dados</h2>
                <p>Não foi possível conectar ao servidor MySQL em <strong>" . htmlspecialchars(DB_HOST) . ":" . htmlspecialchars(DB_PORT) . "</strong> com o usuário <strong>" . htmlspecialchars(DB_USER) . "</strong>.</p>
                <p><strong>Detalhes:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                <hr style='border:0; border-top:1px solid #fecdd3; margin:15px 0;'>
                <p style='font-size:14px; color:#4c0519;'>Certifique-se de que o MySQL (XAMPP, Laragon, Docker ou serviço local) está iniciado e configurado corretamente em <code>config/config.php</code>.</p>
            </div>
        ");
    }
}
