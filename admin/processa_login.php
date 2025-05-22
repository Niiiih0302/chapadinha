<?php
// Inicia a sessão
session_start();

// Inclui arquivos necessários
require_once '../api/v1/config/database.php';
require_once 'includes/Usuario.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    // Valida os campos
    if (empty($usuario) || empty($senha)) {
        $_SESSION['login_erro'] = 'Preencha todos os campos.';
        header('Location: index.php');
        exit;
    }
    
    // Conecta ao banco de dados
    $db = getConnection();
    $usuarioModel = new Usuario($db);
    
    // Verifica as credenciais
    $usuarioData = $usuarioModel->verificarLogin($usuario, $senha);
    
    if ($usuarioData) {
        // Login bem-sucedido, armazena dados na sessão
        $_SESSION['usuario_logado'] = true;
        $_SESSION['usuario_id'] = $usuarioData['id_usuario'];
        $_SESSION['usuario_nome'] = $usuarioData['nome_completo'];
        $_SESSION['usuario_login'] = $usuarioData['usuario'];
        
        // Redireciona para o dashboard
        header('Location: arvores.php');
        exit;
    } else {
        // Login falhou
        $_SESSION['login_erro'] = 'Usuário ou senha inválidos.';
        header('Location: index.php');
        exit;
    }
} else {
    // Acesso direto sem POST
    header('Location: index.php');
    exit;
}