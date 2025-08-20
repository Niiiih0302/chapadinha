<?php
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
     * Busca árvore completa pelo ID (sem biomas e medidas)
     * @param int $id ID da árvore
     * @return array|null
     */
    public function buscarPorId($id) {
        try {
            $id = $this->conn->real_escape_string($id);
            $arvore = null;

            $query_arvore = "SELECT * FROM arvore WHERE id = '$id'";
            $result_arvore = $this->conn->query($query_arvore);
            if (!$result_arvore) {
                throw new Exception("Erro ao buscar árvore principal: " . $this->conn->error);
            }
            if ($result_arvore->num_rows > 0) {
                $arvore = $result_arvore->fetch_assoc();

                // Buscar imagens
                $query_imagens = "SELECT id, caminho_imagem FROM arvore_imagens WHERE fk_arvore = '$id' ORDER BY id";
                $result_imagens = $this->conn->query($query_imagens);
                if (!$result_imagens) throw new Exception("Erro ao buscar imagens: " . $this->conn->error);
                $arvore['imagens'] = [];
                while ($row_img = $result_imagens->fetch_assoc()) {
                    $arvore['imagens'][] = $row_img;
                }

                // Buscar nomes populares
                $query_np = "SELECT nome FROM nome_popular WHERE fk_arvore = '$id'";
                $result_np = $this->conn->query($query_np);
                if (!$result_np) throw new Exception("Erro ao buscar nomes populares: " . $this->conn->error);
                $arvore['nomes_populares'] = [];
                while ($row_np = $result_np->fetch_assoc()) {
                    $arvore['nomes_populares'][] = $row_np['nome'];
                }

                // Buscar tipo de árvore
                $query_tipo = "SELECT exotica_nativa, medicinal, toxica FROM tipo_arvore WHERE fk_arvore = '$id' LIMIT 1";
                $result_tipo = $this->conn->query($query_tipo);
                if (!$result_tipo) throw new Exception("Erro ao buscar tipo de árvore: " . $this->conn->error);
                if ($result_tipo->num_rows > 0) {
                    $arvore['tipo_arvore'] = $result_tipo->fetch_assoc();
                } else {
                    $arvore['tipo_arvore'] = ['exotica_nativa' => null, 'medicinal' => null, 'toxica' => null];
                }
            }
            return $arvore;
        } catch (Exception $e) {
            error_log("Erro ao buscar árvore completa por ID ($id): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista todas as árvores
     * @return array
     */
    public function listarTodas() {
        try {
            $query = "SELECT a.id, a.nome_cientifico, a.familia, a.genero, 
                             (SELECT ai.caminho_imagem 
                              FROM arvore_imagens ai 
                              WHERE ai.fk_arvore = a.id 
                              ORDER BY ai.id ASC 
                              LIMIT 1) as imagem_principal
                      FROM arvore a 
                      ORDER BY a.nome_cientifico";
            $result = $this->conn->query($query);
            if (!$result) {
                throw new Exception("Erro ao listar árvores: " . $this->conn->error);
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
    
    /**
     * Cria uma nova árvore com todos os seus dados relacionados.
     * @param array $dados Dados da árvore e suas relações.
     * @return int|false Retorna o ID da árvore criada ou false em caso de erro.
     */
    public function criar(array $dados) {
        $this->conn->begin_transaction();
        try {
            $nome_cientifico = $this->conn->real_escape_string($dados['nome_cientifico']);
            $familia = isset($dados['familia']) ? $this->conn->real_escape_string($dados['familia']) : NULL;
            $genero = isset($dados['genero']) ? $this->conn->real_escape_string($dados['genero']) : NULL;
            $curiosidade = isset($dados['curiosidade']) ? $this->conn->real_escape_string($dados['curiosidade']) : NULL;

            $query_arvore = "INSERT INTO arvore (nome_cientifico, familia, genero, curiosidade) 
                             VALUES ('$nome_cientifico', ".($familia !== NULL ? "'$familia'" : "NULL").", ".($genero !== NULL ? "'$genero'" : "NULL").", ".($curiosidade !== NULL ? "'$curiosidade'" : "NULL").")";
            if (!$this->conn->query($query_arvore)) {
                throw new Exception("Erro ao criar árvore principal: " . $this->conn->error . " Query: " . $query_arvore);
            }
            $id_arvore = $this->conn->insert_id;

            if (!empty($dados['imagens_novas'])) {
                foreach ($dados['imagens_novas'] as $caminho_imagem) {
                    $caminho_esc = $this->conn->real_escape_string($caminho_imagem);
                    $query_img = "INSERT INTO arvore_imagens (fk_arvore, caminho_imagem) VALUES ('$id_arvore', '$caminho_esc')";
                    if (!$this->conn->query($query_img)) throw new Exception("Erro ao inserir imagem '$caminho_esc': " . $this->conn->error);
                }
            }

            if (!empty($dados['nomes_populares'])) {
                foreach ($dados['nomes_populares'] as $np) {
                    $nome_pop = $this->conn->real_escape_string(trim($np));
                    if (!empty($nome_pop)) {
                        $query_np = "INSERT INTO nome_popular (fk_arvore, nome) VALUES ('$id_arvore', '$nome_pop')";
                        if (!$this->conn->query($query_np)) throw new Exception("Erro ao inserir nome popular '$nome_pop': " . $this->conn->error);
                    }
                }
            }

            if (isset($dados['tipo_arvore'])) {
                $exotica_nativa = isset($dados['tipo_arvore']['exotica_nativa']) && $dados['tipo_arvore']['exotica_nativa'] !== '' ? (int)$dados['tipo_arvore']['exotica_nativa'] : null;
                $medicinal = isset($dados['tipo_arvore']['medicinal']) ? (int)$dados['tipo_arvore']['medicinal'] : 0; 
                $toxica = isset($dados['tipo_arvore']['toxica']) ? (int)$dados['tipo_arvore']['toxica'] : 0; 

                 if ($exotica_nativa !== null || $medicinal !== 0 || $toxica !== 0) { // Verifica se há algo para inserir
                    $exotica_nativa_sql = $exotica_nativa !== null ? "$exotica_nativa" : "NULL"; 
                    $medicinal_sql = "$medicinal"; 
                    $toxica_sql = "$toxica"; 

                    $query_tipo = "INSERT INTO tipo_arvore (fk_arvore, exotica_nativa, medicinal, toxica) 
                                   VALUES ('$id_arvore', $exotica_nativa_sql, $medicinal_sql, $toxica_sql)";
                    if (!$this->conn->query($query_tipo)) throw new Exception("Erro ao inserir tipo de árvore: " . $this->conn->error);
                 }
            }

            $this->conn->commit();
            return $id_arvore;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Erro ao criar árvore e dados relacionados: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza uma árvore existente e seus dados relacionados.
     * @param int $id ID da árvore a ser atualizada.
     * @param array $dados Novos dados da árvore e suas relações.
     * @return bool
     */
    public function atualizar($id, array $dados) {
        $this->conn->begin_transaction();
        try {
            $id_arvore = $this->conn->real_escape_string($id);

            $nome_cientifico = $this->conn->real_escape_string($dados['nome_cientifico']);
            $familia = isset($dados['familia']) ? $this->conn->real_escape_string($dados['familia']) : NULL;
            $genero = isset($dados['genero']) ? $this->conn->real_escape_string($dados['genero']) : NULL;
            $curiosidade = isset($dados['curiosidade']) ? $this->conn->real_escape_string($dados['curiosidade']) : NULL;
            
            $query_arvore = "UPDATE arvore 
                             SET nome_cientifico = '$nome_cientifico', 
                                 familia = ".($familia !== NULL ? "'$familia'" : "NULL").", 
                                 genero = ".($genero !== NULL ? "'$genero'" : "NULL").", 
                                 curiosidade = ".($curiosidade !== NULL ? "'$curiosidade'" : "NULL")."
                             WHERE id = '$id_arvore'";
            if (!$this->conn->query($query_arvore)) {
                throw new Exception("Erro ao atualizar árvore principal: " . $this->conn->error . " Query: " . $query_arvore);
            }

            if (!empty($dados['imagens_novas'])) {
                foreach ($dados['imagens_novas'] as $caminho_imagem) {
                    $caminho_esc = $this->conn->real_escape_string($caminho_imagem);
                    $query_img = "INSERT INTO arvore_imagens (fk_arvore, caminho_imagem) VALUES ('$id_arvore', '$caminho_esc')";
                    if (!$this->conn->query($query_img)) throw new Exception("Erro ao inserir nova imagem '$caminho_esc': " . $this->conn->error);
                }
            }
            
            if (!empty($dados['imagens_para_excluir'])) {
                $ids_para_excluir = array_map('intval', $dados['imagens_para_excluir']);
                $ids_string = implode(',', $ids_para_excluir);
                if (!empty($ids_string)) {
                    $query_del_img = "DELETE FROM arvore_imagens WHERE id IN ($ids_string) AND fk_arvore = '$id_arvore'";
                    if (!$this->conn->query($query_del_img)) throw new Exception("Erro ao excluir imagens antigas: " . $this->conn->error);
                }
            }

            $query_del_np = "DELETE FROM nome_popular WHERE fk_arvore = '$id_arvore'";
            if (!$this->conn->query($query_del_np)) throw new Exception("Erro ao limpar nomes populares antigos: " . $this->conn->error);
            
            if (!empty($dados['nomes_populares'])) {
                foreach ($dados['nomes_populares'] as $np) {
                    $nome_pop = $this->conn->real_escape_string(trim($np));
                     if (!empty($nome_pop)) {
                        $query_np = "INSERT INTO nome_popular (fk_arvore, nome) VALUES ('$id_arvore', '$nome_pop')";
                        if (!$this->conn->query($query_np)) throw new Exception("Erro ao inserir novo nome popular '$nome_pop': " . $this->conn->error);
                    }
                }
            }

            $query_del_tipo = "DELETE FROM tipo_arvore WHERE fk_arvore = '$id_arvore'";
            if (!$this->conn->query($query_del_tipo)) throw new Exception("Erro ao limpar tipo de árvore antigo: " . $this->conn->error);
            if (isset($dados['tipo_arvore'])) {
                $exotica_nativa = isset($dados['tipo_arvore']['exotica_nativa']) && $dados['tipo_arvore']['exotica_nativa'] !== '' ? (int)$dados['tipo_arvore']['exotica_nativa'] : null;
                $medicinal = isset($dados['tipo_arvore']['medicinal']) ? (int)$dados['tipo_arvore']['medicinal'] : 0;
                $toxica = isset($dados['tipo_arvore']['toxica']) ? (int)$dados['tipo_arvore']['toxica'] : 0;

                if ($exotica_nativa !== null || $medicinal !== 0 || $toxica !== 0) {
                    $exotica_nativa_sql = $exotica_nativa !== null ? "$exotica_nativa" : "NULL";
                    $medicinal_sql = "$medicinal";
                    $toxica_sql = "$toxica";
                    $query_tipo = "INSERT INTO tipo_arvore (fk_arvore, exotica_nativa, medicinal, toxica) 
                                   VALUES ('$id_arvore', $exotica_nativa_sql, $medicinal_sql, $toxica_sql)";
                    if (!$this->conn->query($query_tipo)) throw new Exception("Erro ao inserir novo tipo de árvore: " . $this->conn->error);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Erro ao atualizar árvore ($id) e dados relacionados: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui uma árvore.
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
            error_log("Erro ao excluir árvore ($id): " . $e->getMessage());
            return false;
        }
    }
}
?>