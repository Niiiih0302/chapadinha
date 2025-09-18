<?php
session_start();

require_once 'includes/auth_check.php';

require_once '../api/v1/config/database.php';
require_once 'includes/Usuario.php';

function validarDados($nome_completo, $usuario, $senha = null, $confirmar_senha = null, $is_edit = false) {
    $erros = [];
    
    if (empty($nome_completo)) {
        $erros[] = "O nome completo é obrigatório.";
    }
    
    if (empty($usuario)) {
        $erros[] = "O nome de usuário é obrigatório.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $usuario)) {
        $erros[] = "O nome de usuário deve conter apenas letras, números e underscore.";
    }
    
    if (!$is_edit || !empty($senha)) {
        if (empty($senha)) {
            $erros[] = "A senha é obrigatória.";
        } elseif (strlen($senha) < 6) {
            $erros[] = "A senha deve ter pelo menos 6 caracteres.";
        } elseif ($senha !== $confirmar_senha) {
            $erros[] = "As senhas não conferem.";
        }
    }
    
    return $erros;
}

try {
    $db = getConnection();
    
    if ($db->connect_error) {
        throw new Exception("Erro de conexão com banco de dados: " . $db->connect_error);
    }
    
    $usuarioModel = new Usuario($db);
} catch (Exception $e) {
    $_SESSION['usuario_erro'] = "Erro de conexão com o banco de dados: " . $e->getMessage();
    header('Location: usuarios.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    switch ($acao) {
        case 'cadastrar':
            $nome_completo = $_POST['nome_completo'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirmar_senha = $_POST['confirmar_senha'] ?? '';
            
            $erros = validarDados($nome_completo, $usuario, $senha, $confirmar_senha);
            
            if (empty($erros) && $usuarioModel->usuarioExiste($usuario)) {
                $erros[] = "Este nome de usuário já está em uso. Escolha outro.";
            }
            
            if (!empty($erros)) {
                $_SESSION['usuario_erro'] = implode('<br>', $erros);
                header('Location: usuarios.php');
                exit;
            }
            
            if ($usuarioModel->criar($nome_completo, $usuario, $senha)) {
                $_SESSION['usuario_sucesso'] = "Usuário criado com sucesso!";
            } else {
                $_SESSION['usuario_erro'] = "Erro ao criar usuário. Verifique os dados e tente novamente.";
            }
            
            header('Location: usuarios.php');
            exit;
            break;
            
        case 'editar':
            $id = $_POST['id_usuario'] ?? '';
            $nome_completo = $_POST['nome_completo'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirmar_senha = $_POST['confirmar_senha'] ?? '';
            
            $erros = validarDados($nome_completo, $usuario, $senha, $confirmar_senha, true);
            
            if (empty($id)) {
                $erros[] = "ID de usuário inválido.";
            }
            
            if (empty($erros) && $usuarioModel->usuarioExiste($usuario, $id)) {
                $erros[] = "Este nome de usuário já está em uso. Escolha outro.";
            }
            
            if (!empty($erros)) {
                $_SESSION['usuario_erro'] = implode('<br>', $erros);
                header('Location: usuarios.php');
                exit;
            }
            
            if ($usuarioModel->atualizar($id, $nome_completo, $usuario, empty($senha) ? null : $senha)) {
                $_SESSION['usuario_sucesso'] = "Usuário atualizado com sucesso!";
                
                if ($_SESSION['usuario_id'] == $id) {
                    $_SESSION['usuario_nome'] = $nome_completo;
                    $_SESSION['usuario_login'] = $usuario;
                }
            } else {
                $_SESSION['usuario_erro'] = "Erro ao atualizar usuário. Verifique os dados e tente novamente.";
            }
            
            header('Location: usuarios.php');
            exit;
            break;
            
        case 'excluir':
            $id = $_POST['id_usuario'] ?? '';
            
            if (empty($id)) {
                $_SESSION['usuario_erro'] = "ID de usuário inválido.";
                header('Location: usuarios.php');
                exit;
            }
            
            if ($id == $_SESSION['usuario_id']) {
                $_SESSION['usuario_erro'] = "Não é possível excluir o usuário atualmente logado.";
                header('Location: usuarios.php');
                exit;
            }
            
            $usuario = $usuarioModel->buscarPorId($id);
            $nome = $usuario ? $usuario['nome_completo'] : 'Usuário';
            
            if ($usuarioModel->excluir($id)) {
                $_SESSION['usuario_sucesso'] = "Usuário '$nome' excluído com sucesso!";
            } else {
                $_SESSION['usuario_erro'] = "Erro ao excluir usuário. Verifique se ele existe e tente novamente.";
            }
            
            header('Location: usuarios.php');
            exit;
            break;
            
        default:
            $_SESSION['usuario_erro'] = "Ação inválida.";
            header('Location: usuarios.php');
            exit;
    }
} else {
    header('Location: usuarios.php');
    exit;
}