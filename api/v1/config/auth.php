<?php
// Configurações de autenticação
define('API_USERNAME', 'fatec');
define('API_PASSWORD', 'api123');

/**
 * Verifica autenticação Basic Auth
 * @return bool
 */
function autenticarAPI() {
    // Verifica se as credenciais foram enviadas
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="API de Notícias"');
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['status' => 'erro', 'mensagem' => 'Autenticação necessária']);
        return false;
    }
    
    // Verifica se as credenciais estão corretas
    if ($_SERVER['PHP_AUTH_USER'] !== API_USERNAME || $_SERVER['PHP_AUTH_PW'] !== API_PASSWORD) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['status' => 'erro', 'mensagem' => 'Credenciais inválidas']);
        return false;
    }
    
    return true;
}