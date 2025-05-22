<?php
require_once __DIR__ . '/../models/Arvore.php';

/**
 * Controlador para manipular requisições de notícias
 */
class ArvoreController {
    private $arvore;
    private $conn;
    
    /**
     * Construtor
     * @param mysqli $db Conexão com o banco de dados
     */
    public function __construct($db) {
        $this->conn = $db;
        $this->arvore = new Arvore($db);
    }
    
    /**
     * Processa a requisição da API
     * @param string $method Método HTTP
     * @param array $params Parâmetros da requisição
     */
    public function processarRequisicao($method, $params) {
        // Definir cabeçalhos de resposta
        header('Content-Type: application/json; charset=UTF-8');
        
        // Processar com base no método HTTP
        switch ($method) {
            case 'GET':
                $this->processarGET($params);
                break;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido']);
                break;
        }
    }
    
    /**
     * Processa requisições GET
     * @param array $params Parâmetros da requisição
     */
    private function processarGET($params) {
        // Verificar se foi solicitada uma notícia específica pelo ID
        if (isset($params['id'])) {
            $this->getArvore($params['id']);
        } 
        // Verificar se foi solicitada uma lista de notícias por categoria
        else if (isset($params['categoria'])) {
            $this->getArvorePorCategoria($params['categoria']);
        } 
        // Caso contrário, listar todas as notícias
        else {
            $this->listarArvores();
        }
    }
    
    /**
     * Retorna todas as notícias
     */
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
    
    /**
     * Retorna uma notícia específica pelo ID
     * @param int $id ID da notícia
     */
    private function getArvore($id) {
        $arvore = $this->arvore->buscarPorId($id);
        
        if ($arvore) {
            echo json_encode(['status' => 'sucesso', 'arvore' => $arvore]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['status' => 'erro', 'mensagem' => 'Árvore não encontrada']);
        }
    }
    
    // /**
    //  * Retorna notícias de uma categoria específica
    //  * @param int $idCategoria ID da categoria
    //  */
    // private function getArvoresPorCategoria($idCategoria) {
    //     $arvores = $this->arvore->buscarPorCategoria($idCategoria);
        
    //     if (count($arvores) > 0) {
    //         echo json_encode([
    //             'status' => 'sucesso',
    //             'quantidade' => count($arvores),
    //             'categoria_id' => $idCategoria,
    //             'arvores' => $arvores
    //         ]);
    //     } else {
    //         echo json_encode([
    //             'status' => 'sucesso',
    //             'mensagem' => 'Nenhuma árvore encontrada para esta categoria',
    //             'categoria_id' => $idCategoria,
    //             'arvores' => []
    //         ]);
    //     }
    // }
}