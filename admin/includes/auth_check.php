<?php
// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    // Usuário não está logado, redireciona para a página de login
    $_SESSION['login_erro'] = 'Você precisa estar logado para acessar esta página.';
    header('Location: index.php');
    exit;
}