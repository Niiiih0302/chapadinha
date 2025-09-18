<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/controllers/NoticiaController.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (!autenticarAPI()) {
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

$url = isset($_GET['url']) ? $_GET['url'] : '';
$params = [];

$urlParts = explode('/', $url);
if (count($urlParts) > 0) {
    $resource = $urlParts[0];
    
    if (isset($urlParts[1]) && !empty($urlParts[1])) {
        if (is_numeric($urlParts[1])) {
            $params['id'] = $urlParts[1];
        } 
        else if ($urlParts[1] === 'categoria' && isset($urlParts[2]) && is_numeric($urlParts[2])) {
            $params['categoria'] = $urlParts[2];
        }
    }
}

$params = array_merge($params, $_GET);

$conn = getConnection();

if (empty($resource) || $resource === 'noticias') {
    $controller = new NoticiaController($conn);
    $controller->processarRequisicao($method, $params);
} else {
    header('HTTP/1.0 404 Not Found');
    echo json_encode(['status' => 'erro', 'mensagem' => 'Recurso não encontrado']);
}

$conn->close();