<?php
class Categoria {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
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
    
    public function buscarPorId($id) {
        $id = $this->conn->real_escape_string($id);
        
        $query = "SELECT * FROM categorias WHERE id_categoria = '$id'";
        
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
   
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
    
    public function criar($nome, $slug, $descricao) {
        $nome = $this->conn->real_escape_string($nome);
        $slug = $this->conn->real_escape_string($slug);
        $descricao = $this->conn->real_escape_string($descricao);
        
        $query = "INSERT INTO categorias (nome, slug, descricao) 
                  VALUES ('$nome', '$slug', '$descricao')";
        
        return $this->conn->query($query);
    }
    
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
    
    public function excluir($id) {
        $id = $this->conn->real_escape_string($id);
        
        $query = "DELETE FROM categorias WHERE id_categoria = '$id'";
        
        return $this->conn->query($query);
    }
    
    public function contarNoticias($id) {
        $id = $this->conn->real_escape_string($id);
        
        $query = "SELECT COUNT(*) as total FROM noticias WHERE id_categoria = '$id'";
        
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
}