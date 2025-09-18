<?php
define('API_USERNAME', 'fatec');
define('API_PASSWORD', 'api123');

function autenticarAPI() {
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="API de Notícias"');
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['status' => 'erro', 'mensagem' => 'Autenticação necessária']);
        return false;
    }
    
    if ($_SERVER['PHP_AUTH_USER'] !== API_USERNAME || $_SERVER['PHP_AUTH_PW'] !== API_PASSWORD) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['status' => 'erro', 'mensagem' => 'Credenciais inválidas']);
        return false;
    }
    
    return true;
}