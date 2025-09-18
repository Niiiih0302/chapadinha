<?php
session_start();

require_once '../api/v1/config/database.php';
require_once 'includes/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    if (empty($usuario) || empty($senha)) {
        $_SESSION['login_erro'] = 'Preencha todos os campos.';
        header('Location: index.php');
        exit;
    }
    
    $db = getConnection();
    $usuarioModel = new Usuario($db);
    
    $usuarioData = $usuarioModel->verificarLogin($usuario, $senha);
    
    if ($usuarioData) {
        $_SESSION['usuario_logado'] = true;
        $_SESSION['usuario_id'] = $usuarioData['id_usuario'];
        $_SESSION['usuario_nome'] = $usuarioData['nome_completo'];
        $_SESSION['usuario_login'] = $usuarioData['usuario'];
        
        header('Location: arvores.php');
        exit;
    } else {
        $_SESSION['login_erro'] = 'Usuário ou senha inválidos.';
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}