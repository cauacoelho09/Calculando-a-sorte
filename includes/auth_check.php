<?php
/**
 * Verificação de Autenticação
 * Garante que o usuário esteja logado para acessar recursos protegidos
 * Calculando a Sorte
 */

require_once __DIR__ . '/../config/session.php';

if (!isLoggedIn()) {
    setToast('warning', 'Você precisa estar logado para acessar este recurso.');
    header('Location: login.php');
    exit;
}
