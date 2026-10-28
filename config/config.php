<?php
/**
 * Configurações Globais da Aplicação
 * Calculando a Sorte
 */

// Evita redefinição
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Calculando a Sorte');
    define('APP_VERSION', '1.0.0');
    define('APP_DESCRIPTION', 'Plataforma interativa de jogos matemáticos e calculadora da sorte');
    
    // Configurações do Banco de Dados (com suporte a variáveis de ambiente ou padrão XAMPP/Laragon)
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: 'calculando_a_sorte');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
    define('DB_CHARSET', 'utf8mb4');

    // Regras do Jogo e da Sorte
    define('MOEDAS_INICIAIS', 50);
    define('MOEDAS_PREMIO_ANUNCIO', 25);
    define('MOEDAS_PRECO_PREMIUM', 150); // Pode comprar premium com moedas acumuladas ou pagamento fictício
    define('TEMPO_DESBLOQUEIO_ANUNCIO_MINUTOS', 15); // Minutos com todas as operações liberadas após ver anúncio
    define('OPERARCOES_DISPONIVEIS', ['+', '-', '*', '/']);

    // Configuração de Fuso Horário
    date_default_timezone_set('America/Sao_Paulo');
}
