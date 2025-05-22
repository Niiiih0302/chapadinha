<?php
// Arquivo principal da API
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/controllers/NoticiaController.php';

// Habilitar erros para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir cabeçalhos CORS para permitir acesso à API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Verificar autenticação
if (!autenticarAPI()) {
    exit();
}

// Obter método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obter parâmetros da URL
$url = isset($_GET['url']) ? $_GET['url'] : '';
$params = [];

// Extrair parâmetros da URL
$urlParts = explode('/', $url);
if (count($urlParts) > 0) {
    // Primeiro parâmetro é o recurso solicitado (neste caso, 'noticias')
    $resource = $urlParts[0];
    
    // Se tiver um segundo parâmetro, é o ID da notícia ou um filtro
    if (isset($urlParts[1]) && !empty($urlParts[1])) {
        // Se for um número, é um ID
        if (is_numeric($urlParts[1])) {
            $params['id'] = $urlParts[1];
        } 
        // Se for 'categoria', o próximo parâmetro é o ID da categoria
        else if ($urlParts[1] === 'categoria' && isset($urlParts[2]) && is_numeric($urlParts[2])) {
            $params['categoria'] = $urlParts[2];
        }
    }
}

// Mesclar com parâmetros GET da query string
$params = array_merge($params, $_GET);

// Conectar ao banco de dados
$conn = getConnection();

// Processar a requisição com base no recurso solicitado
if (empty($resource) || $resource === 'noticias') {
    $controller = new NoticiaController($conn);
    $controller->processarRequisicao($method, $params);
} else {
    // Recurso não encontrado
    header('HTTP/1.0 404 Not Found');
    echo json_encode(['status' => 'erro', 'mensagem' => 'Recurso não encontrado']);
}

// Fechar conexão com o banco de dados
$conn->close();