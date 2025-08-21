<?php
session_start();
require_once 'includes/auth_check.php';
require_once '../api/v1/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: outros.php');
    exit;
}

$tipo = $_POST['tipo'] ?? '';
if (empty($tipo) || ($tipo !== 'cupim' && $tipo !== 'lagoa')) {
    $_SESSION['item_erro'] = "Tipo de item inválido.";
    header('Location: outros.php');
    exit;
}

$dir_img_save = dirname(dirname(__FILE__)) . '/img/';

try {
    $db = getConnection();
    $db->begin_transaction();

    // Lógica de Upload de Imagem
    $imagem_sql_update = "";
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        // Validações (pode expandir conforme necessário)
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['imagem']['type'], $tipos_permitidos)) {
            throw new Exception("Tipo de arquivo de imagem não permitido.");
        }

        // Gera um nome único para o arquivo
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nome_arquivo = $tipo . '_imagem_' . time() . '.' . $extensao;
        $caminho_arquivo = $dir_img_save . $nome_arquivo;

        if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_arquivo)) {
            throw new Exception("Falha ao mover o arquivo de imagem.");
        }
        $imagem_sql_update = ", imagem = '" . $db->real_escape_string($nome_arquivo) . "'";
    }

    if ($tipo === 'cupim') {
        $sql = "UPDATE cupinzeiro SET 
                    nome_popular = ?, 
                    nome_cientifico = ?, 
                    familia = ?, 
                    genero = ?, 
                    habitat = ?, 
                    dieta = ?, 
                    importancia_ecologica = ?, 
                    curiosidade = ?
                    $imagem_sql_update
                WHERE id = 1";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssssss", 
            $_POST['nome_popular'], 
            $_POST['nome_cientifico'], 
            $_POST['familia'], 
            $_POST['genero'], 
            $_POST['habitat'], 
            $_POST['dieta'], 
            $_POST['importancia_ecologica'], 
            $_POST['curiosidade']
        );
    } elseif ($tipo === 'lagoa') {
        $sql = "UPDATE lagoa SET 
                    nome_popular = ?, 
                    localizacao = ?, 
                    tipo_agua = ?, 
                    fauna_destaque = ?, 
                    flora_destaque = ?, 
                    descricao_geral = ?
                    $imagem_sql_update
                WHERE id = 1";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssss", 
            $_POST['nome_popular'], 
            $_POST['localizacao'], 
            $_POST['tipo_agua'], 
            $_POST['fauna_destaque'], 
            $_POST['flora_destaque'], 
            $_POST['descricao_geral']
        );
    }

    if (!$stmt->execute()) {
        throw new Exception("Erro ao executar a atualização: " . $stmt->error);
    }

    $db->commit();
    $_SESSION['item_sucesso'] = ucfirst($tipo) . " atualizado com sucesso!";

} catch (Exception $e) {
    if (isset($db) && $db->ping()) {
        $db->rollback();
    }
    $_SESSION['item_erro'] = "Erro: " . $e->getMessage();
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($db)) $db->close();
}

header('Location: outros.php');
exit;