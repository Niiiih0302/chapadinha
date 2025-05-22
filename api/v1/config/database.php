<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'chapadinha');

// Conectar ao banco de dados
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    
    // Verificar conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
    
    // Configurar charset para UTF-8
    $conn->set_charset("utf8");
    
    return $conn;
}