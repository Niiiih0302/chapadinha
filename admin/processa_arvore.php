<?php
session_start();

if (!isset($_GET['acao']) || $_GET['acao'] !== 'buscar') {
    require_once 'includes/auth_check.php'; 
}

require_once '../api/v1/config/database.php'; 
require_once 'includes/Arvore.php';        

$base_path = dirname(dirname($_SERVER['SCRIPT_NAME'])); 
$dir_img_save = dirname(dirname(__FILE__)) . '/img/';   

function processarUploadImagens($files, $dir_img_param) {
    $nomes_arquivos_salvos = [];
    $total_files = count($files['name']);

    for ($i = 0; $i < $total_files; $i++) {
        if ($files['error'][$i] == UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($files['error'][$i] != 0) {
            throw new Exception("Erro desconhecido no upload do arquivo (código: {$files['error'][$i]}).");
        }
        
        $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']; 
        if (!in_array($files['type'][$i], $tipos_permitidos)) { 
            throw new Exception("Tipo de arquivo não permitido: " . htmlspecialchars($files['name'][$i])); 
        }
        
        $tamanho_maximo = 5 * 1024 * 1024; 
        if ($files['size'][$i] > $tamanho_maximo) { 
            throw new Exception("O tamanho do arquivo excede 5MB: " . htmlspecialchars($files['name'][$i])); 
        }
        
        $extensao = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION)); 
        $nome_base = pathinfo($files['name'][$i], PATHINFO_FILENAME); 
        
        $nome_base = preg_replace('/[^a-zA-Z0-9_-]/', '', $nome_base); 
        if (empty($nome_base)) { 
            $nome_base = 'imagem_' . date('YmdHis'); 
        }
        
        $nome_arquivo = $nome_base . '.' . $extensao; 
        $contador = 1; 
        
        while (file_exists($dir_img_param . $nome_arquivo)) { 
            $nome_arquivo = $nome_base . '_' . $contador . '.' . $extensao; 
            $contador++; 
        }
        
        $caminho_arquivo = $dir_img_param . $nome_arquivo; 
        
        if (!move_uploaded_file($files['tmp_name'][$i], $caminho_arquivo)) { 
            throw new Exception("Falha ao mover o arquivo para o diretório de destino."); 
        }
        
        $nomes_arquivos_salvos[] = $nome_arquivo;
    }
    
    return $nomes_arquivos_salvos;
}

try {
    $db = getConnection(); 
    if ($db->connect_error) { 
        throw new Exception("Erro de conexão com banco de dados: " . $db->connect_error); 
    }
    $arvoreModel = new Arvore($db); 
} catch (Exception $e) {
    if (isset($_GET['acao']) && $_GET['acao'] === 'buscar') { 
        header('Content-Type: application/json'); 
        echo json_encode(['status' => 'erro', 'mensagem' => "Erro de DB: " . $e->getMessage()]); 
    } else {
        $_SESSION['arvore_erro'] = "Erro de conexão com o banco: " . $e->getMessage(); 
        header('Location: arvores.php'); 
    }
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao']) && $_GET['acao'] === 'buscar') { 
    header('Content-Type: application/json'); 
    $id = $_GET['id'] ?? ''; 
    if (empty($id) || !is_numeric($id)) { 
        echo json_encode(['status' => 'erro', 'mensagem' => 'ID de árvore inválido.']); 
        exit; 
    }
    $arvore = $arvoreModel->buscarPorId($id); 
    if ($arvore) { 
        echo json_encode(['status' => 'sucesso', 'arvore' => $arvore]); 
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Árvore não encontrada ou erro ao buscar dados.']); 
    }
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $acao = $_POST['acao'] ?? ''; 
    $dados_arvore = []; 

    try {
        switch ($acao) { 
            case 'cadastrar': 
            case 'editar': 
                
                $dados_arvore['nome_cientifico'] = $_POST['nome_cientifico'] ?? ''; 
                $dados_arvore['familia'] = !empty($_POST['familia']) ? $_POST['familia'] : null; 
                $dados_arvore['genero'] = !empty($_POST['genero']) ? $_POST['genero'] : null; 
                $dados_arvore['curiosidade'] = !empty($_POST['curiosidade']) ? $_POST['curiosidade'] : null; 

                $dados_arvore['imagens_novas'] = [];
                if (isset($_FILES['imagens_upload']) && !empty($_FILES['imagens_upload']['name'][0])) {
                    $dados_arvore['imagens_novas'] = processarUploadImagens($_FILES['imagens_upload'], $dir_img_save);
                }
                
                if ($acao === 'editar') { 
                    $dados_arvore['id'] = $_POST['id'] ?? ''; 
                    if (empty($dados_arvore['id'])) throw new Exception("ID da árvore não fornecido para edição.");

                    $dados_arvore['imagens_para_excluir'] = $_POST['imagens_excluir'] ?? [];
                    if (!empty($dados_arvore['imagens_para_excluir'])) {
                        $arvoreAtual = $arvoreModel->buscarPorId($dados_arvore['id']);
                        foreach ($arvoreAtual['imagens'] as $img) {
                            if (in_array($img['id'], $dados_arvore['imagens_para_excluir'])) {
                                $caminhoArquivo = $dir_img_save . $img['caminho_imagem'];
                                if (file_exists($caminhoArquivo)) {
                                    @unlink($caminhoArquivo);
                                }
                            }
                        }
                    }
                }
                
                $nomes_populares_input_str = $_POST['nomes_populares_str'] ?? '';
                $dados_arvore['nomes_populares'] = !empty($nomes_populares_input_str) ? array_map('trim', explode(',', $nomes_populares_input_str)) : [];

                $dados_arvore['tipo_arvore'] = [
                    'exotica_nativa' => (isset($_POST['tipo_arvore']['exotica_nativa']) && $_POST['tipo_arvore']['exotica_nativa'] !== '') ? (int)$_POST['tipo_arvore']['exotica_nativa'] : null,
                    'medicinal' => isset($_POST['tipo_arvore']['medicinal']) ? 1 : 0, 
                    'toxica' => isset($_POST['tipo_arvore']['toxica']) ? 1 : 0 
                ];

                if (empty($dados_arvore['nome_cientifico'])) {
                    throw new Exception("O nome científico é obrigatório.");
                }

                if ($acao === 'cadastrar') { 
                    $id_criado = $arvoreModel->criar($dados_arvore);
                    if ($id_criado) {
                        $_SESSION['arvore_sucesso'] = "Árvore (ID: $id_criado) e seus dados foram cadastrados com sucesso!"; 
                    } else {
                        throw new Exception("Erro ao cadastrar árvore e seus dados. Verifique os logs para mais detalhes.");
                    }
                } else { 
                    if ($arvoreModel->atualizar($dados_arvore['id'], $dados_arvore)) {
                        $_SESSION['arvore_sucesso'] = "Árvore (ID: {$dados_arvore['id']}) e seus dados foram atualizados com sucesso!"; 
                    } else {
                        throw new Exception("Erro ao atualizar árvore e seus dados. Verifique os logs para mais detalhes.");
                    }
                }
                break; 

            case 'excluir': 
                $id = $_POST['id'] ?? ''; 
                if (empty($id)) { 
                    throw new Exception("ID da árvore não fornecido para exclusão.");
                }
                
                $arvoreParaExcluir = $arvoreModel->buscarPorId($id); 
                
                if ($arvoreModel->excluir($id)) { 
                    if ($arvoreParaExcluir && !empty($arvoreParaExcluir['imagens'])) {
                       foreach ($arvoreParaExcluir['imagens'] as $img) {
                           $caminhoArquivo = $dir_img_save . $img['caminho_imagem'];
                           if (file_exists($caminhoArquivo)) {
                               @unlink($caminhoArquivo); 
                           }
                       }
                    }
                    $_SESSION['arvore_sucesso'] = "Árvore (ID: $id) excluída com sucesso!"; 
                } else {
                    throw new Exception("Erro ao excluir árvore."); 
                }
                break; 
            
            default: 
                throw new Exception("Ação inválida."); 
        }
    } catch (Exception $e) {
        $_SESSION['arvore_erro'] = "Erro: " . $e->getMessage(); 
    }
    
    header('Location: arvores.php'); 
    exit; 
} else if ($_SERVER['REQUEST_METHOD'] !== 'GET') { 
    $_SESSION['arvore_erro'] = "Método de requisição não suportado."; 
    header('Location: arvores.php'); 
    exit; 
}
else if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!isset($_GET['acao']) || $_GET['acao'] !== 'buscar')) {
    header('Location: arvores.php'); 
    exit; 
}
?>