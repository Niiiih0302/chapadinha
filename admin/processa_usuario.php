<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
require_once 'includes/auth_check.php';

// Inclui arquivos necessários
require_once '../api/v1/config/database.php';
require_once 'includes/Usuario.php';

// Função para validar e sanitizar os dados
function validarDados($nome_completo, $usuario, $senha = null, $confirmar_senha = null, $is_edit = false) {
    $erros = [];
    
    // Valida nome completo
    if (empty($nome_completo)) {
        $erros[] = "O nome completo é obrigatório.";
    }
    
    // Valida usuário
    if (empty($usuario)) {
        $erros[] = "O nome de usuário é obrigatório.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $usuario)) {
        $erros[] = "O nome de usuário deve conter apenas letras, números e underscore.";
    }
    
    // Valida senha (apenas se for cadastro ou estiver alterando a senha)
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

// Conecta ao banco de dados
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

// Processa requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém a ação a ser executada
    $acao = $_POST['acao'] ?? '';
    
    // Processa de acordo com a ação
    switch ($acao) {
        case 'cadastrar':
            // Obtém os dados do formulário
            $nome_completo = $_POST['nome_completo'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirmar_senha = $_POST['confirmar_senha'] ?? '';
            
            // Valida os dados
            $erros = validarDados($nome_completo, $usuario, $senha, $confirmar_senha);
            
            // Verifica se o usuário já existe
            if (empty($erros) && $usuarioModel->usuarioExiste($usuario)) {
                $erros[] = "Este nome de usuário já está em uso. Escolha outro.";
            }
            
            // Se houver erros, redireciona com mensagem
            if (!empty($erros)) {
                $_SESSION['usuario_erro'] = implode('<br>', $erros);
                header('Location: usuarios.php');
                exit;
            }
            
            // Tenta criar o usuário
            if ($usuarioModel->criar($nome_completo, $usuario, $senha)) {
                $_SESSION['usuario_sucesso'] = "Usuário criado com sucesso!";
            } else {
                $_SESSION['usuario_erro'] = "Erro ao criar usuário. Verifique os dados e tente novamente.";
            }
            
            // Redireciona de volta para a listagem
            header('Location: usuarios.php');
            exit;
            break;
            
        case 'editar':
            // Obtém os dados do formulário
            $id = $_POST['id_usuario'] ?? '';
            $nome_completo = $_POST['nome_completo'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirmar_senha = $_POST['confirmar_senha'] ?? '';
            
            // Valida os dados (permitindo senha vazia para não alterar)
            $erros = validarDados($nome_completo, $usuario, $senha, $confirmar_senha, true);
            
            // Verifica se o ID é válido
            if (empty($id)) {
                $erros[] = "ID de usuário inválido.";
            }
            
            // Verifica se o usuário já existe (excluindo o próprio usuário)
            if (empty($erros) && $usuarioModel->usuarioExiste($usuario, $id)) {
                $erros[] = "Este nome de usuário já está em uso. Escolha outro.";
            }
            
            // Se houver erros, redireciona com mensagem
            if (!empty($erros)) {
                $_SESSION['usuario_erro'] = implode('<br>', $erros);
                header('Location: usuarios.php');
                exit;
            }
            
            // Tenta atualizar o usuário
            if ($usuarioModel->atualizar($id, $nome_completo, $usuario, empty($senha) ? null : $senha)) {
                $_SESSION['usuario_sucesso'] = "Usuário atualizado com sucesso!";
                
                // Atualiza os dados da sessão se for o usuário logado
                if ($_SESSION['usuario_id'] == $id) {
                    $_SESSION['usuario_nome'] = $nome_completo;
                    $_SESSION['usuario_login'] = $usuario;
                }
            } else {
                $_SESSION['usuario_erro'] = "Erro ao atualizar usuário. Verifique os dados e tente novamente.";
            }
            
            // Redireciona de volta para a listagem
            header('Location: usuarios.php');
            exit;
            break;
            
        case 'excluir':
            // Obtém o ID do usuário
            $id = $_POST['id_usuario'] ?? '';
            
            // Verifica se o ID é válido
            if (empty($id)) {
                $_SESSION['usuario_erro'] = "ID de usuário inválido.";
                header('Location: usuarios.php');
                exit;
            }
            
            // Verifica se não está tentando excluir o próprio usuário
            if ($id == $_SESSION['usuario_id']) {
                $_SESSION['usuario_erro'] = "Não é possível excluir o usuário atualmente logado.";
                header('Location: usuarios.php');
                exit;
            }
            
            // Busca o usuário para ter o nome para a mensagem de sucesso
            $usuario = $usuarioModel->buscarPorId($id);
            $nome = $usuario ? $usuario['nome_completo'] : 'Usuário';
            
            // Tenta excluir o usuário
            if ($usuarioModel->excluir($id)) {
                $_SESSION['usuario_sucesso'] = "Usuário '$nome' excluído com sucesso!";
            } else {
                $_SESSION['usuario_erro'] = "Erro ao excluir usuário. Verifique se ele existe e tente novamente.";
            }
            
            // Redireciona de volta para a listagem
            header('Location: usuarios.php');
            exit;
            break;
            
        default:
            // Ação inválida
            $_SESSION['usuario_erro'] = "Ação inválida.";
            header('Location: usuarios.php');
            exit;
    }
} else {
    // Acesso direto sem POST
    header('Location: usuarios.php');
    exit;
}