<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado (exceto para a ação 'buscar', que é AJAX)
if (!isset($_GET['acao']) || $_GET['acao'] !== 'buscar') {
    require_once 'includes/auth_check.php';
}

// Inclui arquivos necessários
require_once '../api/v1/config/database.php';
require_once 'includes/Arvore.php';

// Definir o caminho relativo para as imagens (para ajustar URLs recebidas)
$base_path = dirname(dirname($_SERVER['SCRIPT_NAME'])); // Volta um nível (para a raiz do projeto)
$url_img = $base_path . '/img/';
$dir_img = dirname(dirname(__FILE__)) . '/img/'; // Caminho físico para a pasta img

// Função para validar e sanitizar os dados
// function validarDados($curiosidade, $familia, $genero, $nome_cientifico, $imagem = null) {
//     $erros = [];
    
//     // Valida título
//     if (empty($curiosidade)) {
//         $erros[] = "O título da notícia é obrigatório.";
//     }
    
//     // Valida categoria
//     if (empty($id_categoria)) {
//         $erros[] = "Selecione uma categoria para a notícia.";
//     }
    
//     // Valida resumo
//     if (empty($resumo)) {
//         $erros[] = "O resumo da notícia é obrigatório.";
//     }
    
//     // Valida conteúdo
//     if (empty($conteudo)) {
//         $erros[] = "O conteúdo da notícia é obrigatório.";
//     }
    
//     return $erros;
// }

// Função para processar o upload de imagem
function processarUploadImagem($arquivo, $dir_img) {
    // Verifica se houve erro no upload
    if ($arquivo['error'] != 0) {
        switch ($arquivo['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("O arquivo é muito grande. Tamanho máximo permitido: " . ini_get('upload_max_filesize'));
                break;
            case UPLOAD_ERR_PARTIAL:
                throw new Exception("O upload do arquivo foi interrompido.");
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("Nenhum arquivo foi enviado.");
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
            case UPLOAD_ERR_EXTENSION:
                throw new Exception("Erro do servidor ao processar o arquivo.");
                break;
            default:
                throw new Exception("Erro desconhecido no upload do arquivo.");
        }
    }
    
    // Verifica o tipo do arquivo
    $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($arquivo['type'], $tipos_permitidos)) {
        throw new Exception("Tipo de arquivo não permitido. Apenas imagens JPG, PNG e GIF são aceitas.");
    }
    
    // Verificar tamanho do arquivo (máximo de 5MB)
    $tamanho_maximo = 5 * 1024 * 1024; // 5MB em bytes
    if ($arquivo['size'] > $tamanho_maximo) {
        throw new Exception("O tamanho do arquivo excede o limite máximo de 5MB.");
    }
    
    // Gerar nome único para o arquivo para evitar sobrescrita
    $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
    $nome_base = pathinfo($arquivo['name'], PATHINFO_FILENAME);
    
    // Sanitiza o nome do arquivo removendo caracteres especiais e acentos
    $nome_base = preg_replace('/[^a-zA-Z0-9_-]/', '', $nome_base);
    if (empty($nome_base)) {
        $nome_base = 'imagem_' . date('YmdHis');
    }
    
    $nome_arquivo = $nome_base . '.' . $extensao;
    $contador = 1;
    
    // Se o arquivo já existir, adiciona um contador ao nome
    while (file_exists($dir_img . $nome_arquivo)) {
        $nome_arquivo = $nome_base . '_' . $contador . '.' . $extensao;
        $contador++;
    }
    
    // Caminho completo do arquivo
    $caminho_arquivo = $dir_img . $nome_arquivo;
    
    // Tenta mover o arquivo para o diretório de destino
    if (!move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
        throw new Exception("Falha ao mover o arquivo para o diretório de destino.");
    }
    
    // Retorna o nome do arquivo para salvar no banco
    return $nome_arquivo;
}

// Função para normalizar o caminho da imagem para armazenamento no banco
function normalizarCaminhoImagem($imagem) {
    // Se a imagem já é uma URL completa (começa com http:// ou https://), não modifica
    if (strpos($imagem, 'http://') === 0 || strpos($imagem, 'https://') === 0) {
        return $imagem;
    }
    
    // Remove qualquer caminho relativo e mantém apenas o nome do arquivo
    $nome_arquivo = basename($imagem);
    
    // Retorna apenas o nome do arquivo
    return $nome_arquivo;
}

// Conecta ao banco de dados
try {
    $db = getConnection();
    
    if ($db->connect_error) {
        throw new Exception("Erro de conexão com banco de dados: " . $db->connect_error);
    }
    
    $arvoreModel = new Arvore($db);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    exit;
}

// Processsa requisição GET (AJAX para buscar notícia)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao']) && $_GET['acao'] === 'buscar') {
    // Define cabeçalho de resposta JSON
    header('Content-Type: application/json');
    
    // Obtém o ID da notícia
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'ID de árvore inválido.']);
        exit;
    }
    
    // Busca a notícia
    $arvore = $arvoreModel->buscarPorId($id);
    if ($arvore) {
        echo json_encode(['status' => 'sucesso', 'arvore' => $arvore]);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Árvore não encontrada.']);
    }
    
    exit;
}

// Processa requisição POST (formulários)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém a ação a ser executada
    $acao = $_POST['acao'] ?? '';
    
    // Processa de acordo com a ação
    switch ($acao) {
        case 'cadastrar':
            // Obtém os dados do formulário
            $nome_cientifico = $_POST['nome_cientifico'] ?? '';
            $familia = $_POST['familia'] ?? '';
            $genero = $_POST['genero'] ?? '';
            $curiosidade = $_POST['curiosidade'] ?? '';
            $imagem = $_POST['imagem'] ?? '';
            
            // Valida os dados
            // $erros = validarDados($nome_cientifico, $familia, $genero, $curiosidade, $imagem);
            
            // Processa a imagem (upload ou URL)
            try {
                // Verifica se foi enviado um arquivo
                if (isset($_FILES['imagem_upload']) && $_FILES['imagem_upload']['error'] != UPLOAD_ERR_NO_FILE) {
                    // Processa o upload da imagem
                    $imagem = processarUploadImagem($_FILES['imagem_upload'], $dir_img);
                } else if (empty($imagem)) {
                    $erros[] = "É necessário fornecer uma imagem (upload ou URL).";
                } else {
                    // Normaliza o caminho da imagem
                    $imagem = normalizarCaminhoImagem($imagem);
                }
            } catch (Exception $e) {
                $erros[] = "Erro no upload da imagem: " . $e->getMessage();
            }
            
            // Se houver erros, redireciona com mensagem
            if (!empty($erros)) {
                $_SESSION['noticia_erro'] = implode('<br>', $erros);
                header('Location: arvores.php');
                exit;
            }
            
            // Tenta criar a notícia
            if ($arvoreModel->criar($nome_cientifico, $familia, $genero, $curiosidade, $imagem)) {
                $_SESSION['arvore_sucesso'] = "Árvore criada com sucesso!";
            } else {
                $_SESSION['arvore_erro'] = "Erro ao criar árvore. Verifique os dados e tente novamente.";
            }
            
            // Redireciona de volta para a listagem
            header('Location: arvores.php');
            exit;
            break;
            
        case 'editar':
            // Obtém os dados do formulário
            $id = $_POST['id'] ?? '';
            $nome_cientifico = $_POST['nome_cientifico'] ?? '';
            $familia = $_POST['familia'] ?? '';
            $genero = $_POST['genero'] ?? '';
            $curiosidade = $_POST['curiosidade'] ?? '';
            $imagem = $_POST['imagem'] ?? '';
            $imagem_atual = $_POST['imagem_atual'] ?? '';
            
            // Valida os dados
            // $erros = validarDados($titulo, $id_categoria, $resumo, $conteudo);
            
            // Verifica se o ID é válido
            if (empty($id)) {
                $erros[] = "ID de árvore inválido.";
            }
            
            // Processa a imagem (upload, URL ou mantém a atual)
            try {
                // Verifica se foi enviado um arquivo
                if (isset($_FILES['imagem_upload']) && $_FILES['imagem_upload']['error'] != UPLOAD_ERR_NO_FILE) {
                    // Processa o upload da nova imagem
                    $imagem = processarUploadImagem($_FILES['imagem_upload'], $dir_img);
                } else if (!empty($imagem)) {
                    // Normaliza o caminho da nova imagem informada via URL/nome
                    $imagem = normalizarCaminhoImagem($imagem);
                } else {
                    // Mantém a imagem atual
                    $imagem = $imagem_atual;
                }
            } catch (Exception $e) {
                $erros[] = "Erro no upload da imagem: " . $e->getMessage();
            }
            
            // Se houver erros, redireciona com mensagem
            if (!empty($erros)) {
                $_SESSION['arvore_erro'] = implode('<br>', $erros);
                header('Location: arvores.php');
                exit;
            }
            
            // Tenta atualizar a notícia
            if ($arvoreModel->atualizar($id, $nome_cientifico, $familia, $genero, $curiosidade, $imagem)) {
                $_SESSION['arvore_sucesso'] = "Árvore atualizada com sucesso!";
            } else {
                $_SESSION['arvore_erro'] = "Erro ao atualizar notícia. Verifique os dados e tente novamente.";
            }
            
            // Redireciona de volta para a listagem
            header('Location: arvores.php');
            exit;
            break;
            
        case 'excluir':
            // Obtém o ID da notícia
            $id = $_POST['id'] ?? '';
            
            // Verifica se o ID é válido
            if (empty($id)) {
                $_SESSION['arvore_erro'] = "ID de notícia inválido.";
                header('Location: arvores.php');
                exit;
            }
            
            // Tenta excluir a notícia
            if ($arvoreModel->excluir($id)) {
                $_SESSION['arvore_sucesso'] = "Árvore excluída com sucesso!";
            } else {
                $_SESSION['arvore_erro'] = "Erro ao excluir árvore. Verifique se ela existe e tente novamente.";
            }
            
            // Redireciona de volta para a listagem
            header('Location: arvores.php');
            exit;
            break;
            
        default:
            // Ação inválida
            $_SESSION['arvore_erro'] = "Ação inválida.";
            header('Location: arvores.php');
            exit;
    }
} else {
    // Acesso direto sem GET ou POST válidos
    header('Location: arvores.php');
    exit;
}