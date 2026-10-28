-- ====================================================
-- BANCO DE DADOS: Calculando a Sorte
-- Plataforma de jogos matemáticos e calculadora da sorte
-- ====================================================

CREATE DATABASE IF NOT EXISTS `calculando_a_sorte` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `calculando_a_sorte`;

-- ----------------------------------------------------
-- 1. TABELA DE USUÁRIOS
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NOT NULL,
    `is_premium` TINYINT(1) NOT NULL DEFAULT 0,
    `moedas` INT NOT NULL DEFAULT 50,
    `operacao_atual` ENUM('+', '-', '*', '/') NOT NULL DEFAULT '+',
    `operacoes_desbloqueadas` VARCHAR(50) NOT NULL DEFAULT '+',
    `anuncio_desbloqueio_ate` DATETIME NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_premium` (`is_premium`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------
-- 2. TABELA DE LOGS DE AUDITORIA (Conforme Regras de Negócio)
-- Mantém logs detalhados de qualquer alteração de qualquer usuário
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `acao` VARCHAR(50) NOT NULL,
    `descricao` TEXT NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_acao` (`acao`),
    INDEX `idx_created_at` (`created_at`),
    CONSTRAINT `fk_logs_usuario` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `usuarios` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------
-- 3. TABELA DE HISTÓRICO DE CÁLCULOS
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `calculos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `expressao` VARCHAR(100) NOT NULL,
    `operacao` CHAR(1) NOT NULL,
    `resultado` DECIMAL(15, 4) NOT NULL,
    `tipo_desbloqueio` ENUM('sorteio', 'anuncio', 'premium') NOT NULL DEFAULT 'sorteio',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_calc_user` (`user_id`),
    INDEX `idx_calc_data` (`created_at`),
    CONSTRAINT `fk_calculos_usuario` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `usuarios` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------
-- 4. TABELA DE HISTÓRICO DE JOGOS MATEMÁTICOS
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `jogos_historico` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `tipo_jogo` VARCHAR(50) NOT NULL,
    `pontos` INT NOT NULL DEFAULT 0,
    `acertos` INT NOT NULL DEFAULT 0,
    `erros` INT NOT NULL DEFAULT 0,
    `moedas_ganhas` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_jogo_user` (`user_id`),
    INDEX `idx_jogo_tipo` (`tipo_jogo`),
    INDEX `idx_jogo_pontos` (`pontos`),
    CONSTRAINT `fk_jogos_usuario` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `usuarios` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------
-- USUÁRIOS DE EXEMPLO PARA TESTES RÁPIDOS
-- Senha de ambos é: "123456" (hash bcrypt padrão gerado)
-- ----------------------------------------------------
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `is_premium`, `moedas`, `operacao_atual`, `operacoes_desbloqueadas`) 
VALUES 
(1, 'Jogador Teste', 'jogador@sorte.com', '$2y$10$uA3f03bQ1jW3L38pQvT87e3yK7rV8l5n.G7q.3x.u2v8q9q2h5dE2', 0, 100, '+', '+'),
(2, 'Mestre VIP', 'vip@sorte.com', '$2y$10$uA3f03bQ1jW3L38pQvT87e3yK7rV8l5n.G7q.3x.u2v8q9q2h5dE2', 1, 500, '+', '+,-,*,/')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Inserir logs iniciais do sistema
INSERT INTO `logs` (`user_id`, `acao`, `descricao`, `ip_address`, `user_agent`) 
VALUES 
(1, 'SISTEMA', 'Banco de dados e usuários de demonstração inicializados com sucesso', '127.0.0.1', 'Database Init'),
(2, 'SISTEMA', 'Usuário VIP cadastrado com permissões totais nas operações básicas', '127.0.0.1', 'Database Init');
