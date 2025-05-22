<?php
/**
 * Classe Noticia - Modelo para acesso aos dados de notícias
 */
class Noticia {
    private $conn;
    
    /**
     * Construtor
     * @param mysqli $db Conexão com o banco de dados
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Busca todas as notícias
     * @return array
     */
    public function listarTodas() {
        $query = "SELECT n.*, c.nome as categoria FROM noticias n
                  JOIN categorias c ON n.id_categoria = c.id_categoria
                  ORDER BY n.data_publicacao DESC";
        
        $result = $this->conn->query($query);
        $noticias = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $noticias[] = $row;
            }
        }
        
        return $noticias;
    }
    
    /**
     * Busca notícia pelo ID
     * @param int $id ID da notícia
     * @return array|null
     */
    public function buscarPorId($id) {
        $id = $this->conn->real_escape_string($id);
        
        $query = "SELECT n.*, c.nome as categoria FROM noticias n
                  JOIN categorias c ON n.id_categoria = c.id_categoria
                  WHERE n.id_noticia = '$id'";
        
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Busca notícias por categoria
     * @param int $idCategoria ID da categoria
     * @return array
     */
    public function buscarPorCategoria($idCategoria) {
        $idCategoria = $this->conn->real_escape_string($idCategoria);
        
        $query = "SELECT n.*, c.nome as categoria FROM noticias n
                  JOIN categorias c ON n.id_categoria = c.id_categoria
                  WHERE n.id_categoria = '$idCategoria'
                  ORDER BY n.data_publicacao DESC";
        
        $result = $this->conn->query($query);
        $noticias = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $noticias[] = $row;
            }
        }
        
        return $noticias;
    }
}