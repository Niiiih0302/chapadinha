<?php
/**
 * Classe Categoria - Modelo para acesso aos dados de categorias
 */
class Categoria {
    private $conn;
    
    /**
     * Construtor
     * @param mysqli $db Conexão com o banco de dados
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Lista todas as categorias
     * @return array
     */
    public function listarTodas() {
        $query = "SELECT * FROM categorias ORDER BY nome ASC";
        
        $result = $this->conn->query($query);
        $categorias = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
        }
        
        return $categorias;
    }
    
    /**
     * Busca categoria pelo ID
     * @param int $id ID da categoria
     * @return array|null
     */
    public function buscarPorId($id) {
        $id = $this->conn->real_escape_string($id);
        
        $query = "SELECT * FROM categorias WHERE id_categoria = '$id'";
        
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Verifica se o slug já existe
     * @param string $slug Slug a verificar
     * @param int|null $excluirId ID a excluir da verificação (para edição)
     * @return bool
     */
    public function slugExiste($slug, $excluirId = null) {
        $slug = $this->conn->real_escape_string($slug);
        
        $query = "SELECT id_categoria FROM categorias WHERE slug = '$slug'";
        
        if ($excluirId !== null) {
            $excluirId = $this->conn->real_escape_string($excluirId);
            $query .= " AND id_categoria != '$excluirId'";
        }
        
        $result = $this->conn->query($query);
        
        return $result->num_rows > 0;
    }
    
    /**
     * Cria uma nova categoria
     * @param string $nome Nome da categoria
     * @param string $slug Slug da categoria
     * @param string $descricao Descrição da categoria
     * @return bool
     */
    public function criar($nome, $slug, $descricao) {
        $nome = $this->conn->real_escape_string($nome);
        $slug = $this->conn->real_escape_string($slug);
        $descricao = $this->conn->real_escape_string($descricao);
        
        $query = "INSERT INTO categorias (nome, slug, descricao) 
                  VALUES ('$nome', '$slug', '$descricao')";
        
        return $this->conn->query($query);
    }
    
    /**
     * Atualiza uma categoria existente
     * @param int $id ID da categoria
     * @param string $nome Nome da categoria
     * @param string $slug Slug da categoria
     * @param string $descricao Descrição da categoria
     * @return bool
     */
    public function atualizar($id, $nome, $slug, $descricao) {
        $id = $this->conn->real_escape_string($id);
        $nome = $this->conn->real_escape_string($nome);
        $slug = $this->conn->real_escape_string($slug);
        $descricao = $this->conn->real_escape_string($descricao);
        
        $query = "UPDATE categorias 
                  SET nome = '$nome', slug = '$slug', descricao = '$descricao' 
                  WHERE id_categoria = '$id'";
        
        return $this->conn->query($query);
    }
    
    /**
     * Exclui uma categoria
     * @param int $id ID da categoria
     * @return bool
     */
    public function excluir($id) {
        $id = $this->conn->real_escape_string($id);
        
        $query = "DELETE FROM categorias WHERE id_categoria = '$id'";
        
        return $this->conn->query($query);
    }
    
    /**
     * Conta o número de notícias associadas a uma categoria
     * @param int $id ID da categoria
     * @return int
     */
    public function contarNoticias($id) {
        $id = $this->conn->real_escape_string($id);
        
        $query = "SELECT COUNT(*) as total FROM noticias WHERE id_categoria = '$id'";
        
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
}