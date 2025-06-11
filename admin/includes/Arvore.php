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
     * Busca árvore completa pelo ID (incluindo dados de tabelas relacionadas)
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

                $query_np = "SELECT nome FROM nome_popular WHERE fk_arvore = '$id'";
                $result_np = $this->conn->query($query_np);
                if (!$result_np) throw new Exception("Erro ao buscar nomes populares: " . $this->conn->error);
                $arvore['nomes_populares'] = [];
                while ($row_np = $result_np->fetch_assoc()) {
                    $arvore['nomes_populares'][] = $row_np['nome'];
                }

                $query_biomas_nomes = "SELECT b.nome 
                                       FROM arvore_bioma ab
                                       JOIN bioma b ON ab.fk_bioma = b.id
                                       WHERE ab.fk_arvore = '$id'";
                $result_biomas_nomes = $this->conn->query($query_biomas_nomes);
                if (!$result_biomas_nomes) throw new Exception("Erro ao buscar nomes dos biomas da árvore: " . $this->conn->error);
                $arvore['biomas_nomes'] = []; 
                while ($row_bn = $result_biomas_nomes->fetch_assoc()) {
                    $arvore['biomas_nomes'][] = $row_bn['nome'];
                }

                $query_medidas = "SELECT CAP, DAP, amortizacao FROM medidas WHERE fk_arvore = '$id' LIMIT 1";
                $result_medidas = $this->conn->query($query_medidas);
                if (!$result_medidas) throw new Exception("Erro ao buscar medidas: " . $this->conn->error);
                if ($result_medidas->num_rows > 0) {
                    $arvore['medidas'] = $result_medidas->fetch_assoc();
                } else {
                    $arvore['medidas'] = ['CAP' => null, 'DAP' => null, 'amortizacao' => null];
                }

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
     * Lista todas as árvores (dados básicos para a tabela de administração)
     * @return array
     */
    public function listarTodas() {
        try {
            $query = "SELECT id, nome_cientifico, familia, genero, imagem FROM arvore ORDER BY nome_cientifico"; //
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
     * Busca ou cria um bioma pelo nome e retorna seu ID.
     * @param string $nomeBioma Nome do bioma
     * @return int|false ID do bioma ou false em caso de erro.
     */
    private function obterIdBioma($nomeBioma) {
        $nomeBiomaEsc = $this->conn->real_escape_string(trim($nomeBioma));
        if (empty($nomeBiomaEsc)) {
            return false;
        }

        $query_select = "SELECT id FROM bioma WHERE nome = '$nomeBiomaEsc'";
        $result = $this->conn->query($query_select);
        if (!$result) {
            throw new Exception("Erro ao buscar bioma '$nomeBiomaEsc': " . $this->conn->error);
        }

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['id'];
        } else {
            $query_insert = "INSERT INTO bioma (nome, descricao) VALUES ('$nomeBiomaEsc', NULL)";
            if (!$this->conn->query($query_insert)) {
                throw new Exception("Erro ao criar novo bioma '$nomeBiomaEsc': " . $this->conn->error);
            }
            return (int)$this->conn->insert_id;
        }
    }


    /**
     * Cria uma nova árvore com todos os seus dados relacionados.
     * @param array $dados Dados da árvore e suas relações.
     * 'biomas_nomes' deve ser um array de strings com os nomes dos biomas.
     * @return int|false Retorna o ID da árvore criada ou false em caso de erro.
     */
    public function criar(array $dados) {
        $this->conn->begin_transaction();
        try {
            $nome_cientifico = $this->conn->real_escape_string($dados['nome_cientifico']);
            $familia = isset($dados['familia']) ? $this->conn->real_escape_string($dados['familia']) : NULL;
            $genero = isset($dados['genero']) ? $this->conn->real_escape_string($dados['genero']) : NULL;
            $curiosidade = isset($dados['curiosidade']) ? $this->conn->real_escape_string($dados['curiosidade']) : NULL;
            $imagem = isset($dados['imagem']) ? $this->conn->real_escape_string($dados['imagem']) : NULL;

            $query_arvore = "INSERT INTO arvore (nome_cientifico, familia, genero, curiosidade, imagem) 
                             VALUES ('$nome_cientifico', ".($familia !== NULL ? "'$familia'" : "NULL").", ".($genero !== NULL ? "'$genero'" : "NULL").", ".($curiosidade !== NULL ? "'$curiosidade'" : "NULL").", ".($imagem !== NULL ? "'$imagem'" : "NULL").")";
            if (!$this->conn->query($query_arvore)) {
                throw new Exception("Erro ao criar árvore principal: " . $this->conn->error . " Query: " . $query_arvore);
            }
            $id_arvore = $this->conn->insert_id;


            if (!empty($dados['nomes_populares'])) {
                foreach ($dados['nomes_populares'] as $np) {
                    $nome_pop = $this->conn->real_escape_string(trim($np));
                    if (!empty($nome_pop)) {
                        $query_np = "INSERT INTO nome_popular (fk_arvore, nome) VALUES ('$id_arvore', '$nome_pop')";
                        if (!$this->conn->query($query_np)) throw new Exception("Erro ao inserir nome popular '$nome_pop': " . $this->conn->error);
                    }
                }
            }


            if (!empty($dados['biomas_nomes'])) {
                foreach ($dados['biomas_nomes'] as $nome_bioma_str) {
                    $id_bioma = $this->obterIdBioma($nome_bioma_str);
                    if ($id_bioma) {
                        $query_ab = "INSERT INTO arvore_bioma (fk_arvore, fk_bioma) VALUES ('$id_arvore', '$id_bioma')";
                        if (!$this->conn->query($query_ab)) throw new Exception("Erro ao associar bioma '$nome_bioma_str' (ID: $id_bioma): " . $this->conn->error);
                    }
                }
            }



            if (isset($dados['medidas'])) {
                $cap = isset($dados['medidas']['CAP']) && $dados['medidas']['CAP'] !== '' ? $this->conn->real_escape_string($dados['medidas']['CAP']) : null;
                $dap = isset($dados['medidas']['DAP']) && $dados['medidas']['DAP'] !== '' ? $this->conn->real_escape_string($dados['medidas']['DAP']) : null;
                $amortizacao = isset($dados['medidas']['amortizacao']) && $dados['medidas']['amortizacao'] !== '' ? $this->conn->real_escape_string($dados['medidas']['amortizacao']) : null;

                if ($cap !== null || $dap !== null || $amortizacao !== null) {
                    $cap_sql = $cap !== null ? "'$cap'" : "NULL";
                    $dap_sql = $dap !== null ? "'$dap'" : "NULL";
                    $amortizacao_sql = $amortizacao !== null ? "'$amortizacao'" : "NULL";

                    $query_medidas = "INSERT INTO medidas (fk_arvore, CAP, DAP, amortizacao) 
                                      VALUES ('$id_arvore', $cap_sql, $dap_sql, $amortizacao_sql)";
                    if (!$this->conn->query($query_medidas)) throw new Exception("Erro ao inserir medidas: " . $this->conn->error);
                }
            }


            if (isset($dados['tipo_arvore'])) {
                $exotica_nativa = isset($dados['tipo_arvore']['exotica_nativa']) && $dados['tipo_arvore']['exotica_nativa'] !== '' ? (int)$dados['tipo_arvore']['exotica_nativa'] : null;
                $medicinal = isset($dados['tipo_arvore']['medicinal']) ? (int)$dados['tipo_arvore']['medicinal'] : 0; 
                $toxica = isset($dados['tipo_arvore']['toxica']) ? (int)$dados['tipo_arvore']['toxica'] : 0; 


                 if ($exotica_nativa !== null || $medicinal !== null || $toxica !== null) { // Medicinal e toxica agora sempre terão valor (0 ou 1)
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
     * 'biomas_nomes' deve ser um array de strings com os nomes dos biomas.
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
            
            $imagem_sql_part = "";
            if (array_key_exists('imagem', $dados)) { 
                 $imagem = $dados['imagem'] !== null ? $this->conn->real_escape_string($dados['imagem']) : null;
                 $imagem_sql_part = ", imagem = " . ($imagem !== null ? "'$imagem'" : "NULL");
            }

            $query_arvore = "UPDATE arvore 
                             SET nome_cientifico = '$nome_cientifico', 
                                 familia = ".($familia !== NULL ? "'$familia'" : "NULL").", 
                                 genero = ".($genero !== NULL ? "'$genero'" : "NULL").", 
                                 curiosidade = ".($curiosidade !== NULL ? "'$curiosidade'" : "NULL")."
                                 $imagem_sql_part
                             WHERE id = '$id_arvore'";
            if (!$this->conn->query($query_arvore)) {
                throw new Exception("Erro ao atualizar árvore principal: " . $this->conn->error . " Query: " . $query_arvore);
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

            $query_del_ab = "DELETE FROM arvore_bioma WHERE fk_arvore = '$id_arvore'";
            if (!$this->conn->query($query_del_ab)) throw new Exception("Erro ao limpar biomas antigos da árvore: " . $this->conn->error);
            
            if (!empty($dados['biomas_nomes'])) {
                foreach ($dados['biomas_nomes'] as $nome_bioma_str) {
                     $id_bioma = $this->obterIdBioma($nome_bioma_str);
                    if ($id_bioma) {
                        $query_ab = "INSERT INTO arvore_bioma (fk_arvore, fk_bioma) VALUES ('$id_arvore', '$id_bioma')";
                        if (!$this->conn->query($query_ab)) throw new Exception("Erro ao associar bioma '$nome_bioma_str' (ID: $id_bioma) na atualização: " . $this->conn->error);
                    }
                }
            }


            $query_del_medidas = "DELETE FROM medidas WHERE fk_arvore = '$id_arvore'";
            if (!$this->conn->query($query_del_medidas)) throw new Exception("Erro ao limpar medidas antigas: " . $this->conn->error);
            if (isset($dados['medidas'])) {
                $cap = isset($dados['medidas']['CAP']) && $dados['medidas']['CAP'] !== '' ? $this->conn->real_escape_string($dados['medidas']['CAP']) : null;
                $dap = isset($dados['medidas']['DAP']) && $dados['medidas']['DAP'] !== '' ? $this->conn->real_escape_string($dados['medidas']['DAP']) : null;
                $amortizacao = isset($dados['medidas']['amortizacao']) && $dados['medidas']['amortizacao'] !== '' ? $this->conn->real_escape_string($dados['medidas']['amortizacao']) : null;

                if ($cap !== null || $dap !== null || $amortizacao !== null) {
                    $cap_sql = $cap !== null ? "'$cap'" : "NULL";
                    $dap_sql = $dap !== null ? "'$dap'" : "NULL";
                    $amortizacao_sql = $amortizacao !== null ? "'$amortizacao'" : "NULL";
                    $query_medidas = "INSERT INTO medidas (fk_arvore, CAP, DAP, amortizacao) 
                                      VALUES ('$id_arvore', $cap_sql, $dap_sql, $amortizacao_sql)";
                    if (!$this->conn->query($query_medidas)) throw new Exception("Erro ao inserir novas medidas: " . $this->conn->error);
                }
            }

            $query_del_tipo = "DELETE FROM tipo_arvore WHERE fk_arvore = '$id_arvore'";
            if (!$this->conn->query($query_del_tipo)) throw new Exception("Erro ao limpar tipo de árvore antigo: " . $this->conn->error);
            if (isset($dados['tipo_arvore'])) {
                $exotica_nativa = isset($dados['tipo_arvore']['exotica_nativa']) && $dados['tipo_arvore']['exotica_nativa'] !== '' ? (int)$dados['tipo_arvore']['exotica_nativa'] : null;
                $medicinal = isset($dados['tipo_arvore']['medicinal']) ? (int)$dados['tipo_arvore']['medicinal'] : 0;
                $toxica = isset($dados['tipo_arvore']['toxica']) ? (int)$dados['tipo_arvore']['toxica'] : 0;

                if ($exotica_nativa !== null || $medicinal !== null || $toxica !== null) { // Medicinal e toxica sempre terão valor
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
            $query = "DELETE FROM arvore WHERE id = '$id'"; //
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