<?php
require_once __DIR__ . '/../models/Arvore.php';

class ArvoreController {
    private $arvore;
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->arvore = new Arvore($db);
    }
    
    public function processarRequisicao($method, $params) {
        header('Content-Type: application/json; charset=UTF-8');
        
        switch ($method) {
            case 'GET':
                $this->processarGET($params);
                break;
            default:
                http_response_code(405); 
                echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido']);
                break;
        }
    }
    

    private function processarGET($params) {
        if (isset($params['id'])) {
            $this->getArvore($params['id']);
        } 
        else if (isset($params['categoria'])) {
            $this->getArvorePorCategoria($params['categoria']);
        } 
        else {
            $this->listarArvores();
        }
    }
    
    private function listarArvores() {
        $arvores = $this->arvore->listarTodas();
        
        if (count($arvores) > 0) {
            echo json_encode([
                'status' => 'sucesso',
                'quantidade' => count($arvores),
                'arvores' => $arvores
            ]);
        } else {
            echo json_encode(['status' => 'sucesso', 'mensagem' => 'Nenhuma árvore encontrada', 'Árvores' => []]);
        }
    }
    
    private function getArvore($id) {
        $arvore = $this->arvore->buscarPorId($id);
        
        if ($arvore) {
            echo json_encode(['status' => 'sucesso', 'arvore' => $arvore]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'erro', 'mensagem' => 'Árvore não encontrada']);
        }
    }
}