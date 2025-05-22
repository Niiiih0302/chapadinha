<?php
/**
 * Classe Noticia - Modelo para acesso administrativo aos dados de notícias
 */
class Arvore {
    private $conn;
    /**
     * Construtor
     * @param mysqli $db Conexão com o banco de dados
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Busca notícia pelo ID
     * @param int $id ID da notícia
     * @return array|null
     */
    public function buscarPorId($id) {
        try {
            $id = $this->conn->real_escape_string($id);
            
            $query = "SELECT * FROM arvore WHERE id = '$id'";
            
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Erro ao buscar árvore: " . $this->conn->error);
            }
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Erro ao buscar árvore por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lista todas as notícias
     * @return array
     */
    public function listarTodas() {
        try {
            $query = "SELECT * FROM arvore ORDER BY nome_cientifico";
            
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Erro ao listar árvore: " . $this->conn->error);
            }
            
            $arvores = [];
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $arvores[] = $row;
                }
            }
            
            return $arvores;
        } catch (Exception $e) {
            error_log("Erro ao listar árvores: " . $e->getMessage());
            return [];
        }
    }
    
    // /**
    //  * Busca notícias por categoria
    //  * @param int $idCategoria ID da categoria
    //  * @return array
    //  */
    // public function buscarPorCategoria($idCategoria) {
    //     try {
    //         $idCategoria = $this->conn->real_escape_string($idCategoria);
            
    //         $query = "SELECT n.*, c.nome as categoria FROM noticias n
    //                   JOIN categorias c ON n.id_categoria = c.id_categoria
    //                   WHERE n.id_categoria = '$idCategoria'
    //                   ORDER BY n.data_publicacao DESC";
            
    //         $result = $this->conn->query($query);
            
    //         if (!$result) {
    //             throw new Exception("Erro ao buscar notícias por categoria: " . $this->conn->error);
    //         }
            
    //         $noticias = [];
            
    //         if ($result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $noticias[] = $row;
    //             }
    //         }
            
    //         return $noticias;
    //     } catch (Exception $e) {
    //         error_log("Erro ao buscar notícias por categoria: " . $e->getMessage());
    //         return [];
    //     }
    // }
    
    // /**
    //  * Busca notícias em destaque
    //  * @return array
    //  */
    // public function buscarDestaques() {
    //     try {
    //         $query = "SELECT n.*, c.nome as categoria FROM noticias n
    //                   JOIN categorias c ON n.id_categoria = c.id_categoria
    //                   WHERE n.destaque = 1
    //                   ORDER BY n.data_publicacao DESC";
            
    //         $result = $this->conn->query($query);
            
    //         if (!$result) {
    //             throw new Exception("Erro ao buscar notícias em destaque: " . $this->conn->error);
    //         }
            
    //         $noticias = [];
            
    //         if ($result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $noticias[] = $row;
    //             }
    //         }
            
    //         return $noticias;
    //     } catch (Exception $e) {
    //         error_log("Erro ao buscar notícias em destaque: " . $e->getMessage());
    //         return [];
    //     }
    // }
    
    // /**
    //  * Conta o total de notícias
    //  * @return int
    //  */
    // public function contarTotal() {
    //     try {
    //         $query = "SELECT COUNT(*) as total FROM noticias";
            
    //         $result = $this->conn->query($query);
            
    //         if (!$result) {
    //             throw new Exception("Erro ao contar notícias: " . $this->conn->error);
    //         }
            
    //         $row = $result->fetch_assoc();
    //         return (int)$row['total'];
    //     } catch (Exception $e) {
    //         error_log("Erro ao contar notícias: " . $e->getMessage());
    //         return 0;
    //     }
    // }
    
    // /**
    //  * Conta o total de notícias em destaque
    //  * @return int
    //  */
    // public function contarDestaques() {
    //     try {
    //         $query = "SELECT COUNT(*) as total FROM noticias WHERE destaque = 1";
            
    //         $result = $this->conn->query($query);
            
    //         if (!$result) {
    //             throw new Exception("Erro ao contar notícias em destaque: " . $this->conn->error);
    //         }
            
    //         $row = $result->fetch_assoc();
    //         return (int)$row['total'];
    //     } catch (Exception $e) {
    //         error_log("Erro ao contar notícias em destaque: " . $e->getMessage());
    //         return 0;
    //     }
    // }
    
        /**
     * Cria uma nova árvore
     * @param string $nome_cientifico Nome científico da árvore
     * @param string $familia Família da árvore
     * @param string $genero Gênero da árvore
     * @param string $curiosidade Curiosidade sobre a árvore
     * @param string $imagem URL da imagem
     * @return bool
     */
    public function criar($nome_cientifico, $familia, $genero, $curiosidade, $imagem) {
        try {
            $nome_cientifico = $this->conn->real_escape_string($nome_cientifico);
            $familia = $this->conn->real_escape_string($familia);
            $genero = $this->conn->real_escape_string($genero);
            $curiosidade = $this->conn->real_escape_string($curiosidade);
            $imagem = $this->conn->real_escape_string($imagem);

            $query = "INSERT INTO arvore (nome_cientifico, familia, genero, curiosidade, imagem) 
                    VALUES ('$nome_cientifico', '$familia', '$genero', '$curiosidade', '$imagem')";

            $result = $this->conn->query($query);
            if (!$result) {
                throw new Exception("Erro ao criar árvore: " . $this->conn->error);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao criar árvore: " . $e->getMessage());
            return false;
        }
    }

    
        /**
     * Atualiza uma árvore existente
     * @param int $id ID da árvore
     * @param string $nome_cientifico Nome científico da árvore
     * @param string $familia Família da árvore
     * @param string $genero Gênero da árvore
     * @param string $curiosidade Curiosidade sobre a árvore
     * @param string $imagem URL da imagem
     * @return bool
     */
    public function atualizar($id, $nome_cientifico, $familia, $genero, $curiosidade, $imagem) {
        try {
            $id = $this->conn->real_escape_string($id);
            $nome_cientifico = $this->conn->real_escape_string($nome_cientifico);
            $familia = $this->conn->real_escape_string($familia);
            $genero = $this->conn->real_escape_string($genero);
            $curiosidade = $this->conn->real_escape_string($curiosidade);
            $imagem = $this->conn->real_escape_string($imagem);

            $query = "UPDATE arvore 
                    SET nome_cientifico = '$nome_cientifico', 
                        familia = '$familia', 
                        genero = '$genero', 
                        curiosidade = '$curiosidade', 
                        imagem = '$imagem' 
                    WHERE id = '$id'";

            $result = $this->conn->query($query);

            if (!$result) {
                throw new Exception("Erro ao atualizar árvore: " . $this->conn->error);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao atualizar árvore: " . $e->getMessage());
            return false;
        }
    }

    
        /**
     * Exclui uma árvore
     * @param int $id ID da árvore
     * @return bool
     */
    public function excluir($id) {
        try {
            $id = $this->conn->real_escape_string($id);
            $query = "DELETE FROM arvore WHERE id = '$id'";

            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Erro ao excluir árvore: " . $this->conn->error);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao excluir árvore: " . $e->getMessage());
            return false;
        }
    }

}