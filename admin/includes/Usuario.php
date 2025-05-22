<?php
/**
 * Classe Usuario - Modelo para acesso aos dados de usuários
 */
class Usuario {
    private $conn;
    
    /**
     * Construtor
     * @param mysqli $db Conexão com o banco de dados
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Verifica as credenciais de login
     * @param string $usuario Nome de usuário
     * @param string $senha Senha do usuário
     * @return array|null Dados do usuário ou null se inválido
     */
    public function verificarLogin($usuario, $senha) {
        try {
            // Usar prepared statements para evitar SQL injection
            $query = "SELECT id_usuario, nome_completo, usuario, senha FROM usuarios WHERE usuario = ?";
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Erro na preparação da consulta: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Verifica se a senha está correta usando password_verify
                if (password_verify($senha, $user['senha'])) {
                    unset($user['senha']); // Remove a senha do array de retorno
                    return $user;
                }
            }
            
            return null;
        } catch (Exception $e) {
            // Você pode logar o erro ou tratá-lo conforme necessário
            error_log("Erro ao verificar login: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verifica se um nome de usuário já existe
     * @param string $usuario Nome de usuário
     * @param int|null $excluirId ID a excluir da verificação (para edição)
     * @return bool
     */
    public function usuarioExiste($usuario, $excluirId = null) {
        try {
            $usuario = $this->conn->real_escape_string($usuario);
            
            $query = "SELECT id_usuario FROM usuarios WHERE usuario = '$usuario'";
            
            if ($excluirId !== null) {
                $excluirId = $this->conn->real_escape_string($excluirId);
                $query .= " AND id_usuario != '$excluirId'";
            }
            
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Erro ao verificar usuário: " . $this->conn->error);
            }
            
            return $result->num_rows > 0;
        } catch (Exception $e) {
            error_log("Erro ao verificar existência de usuário: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca usuário pelo ID
     * @param int $id ID do usuário
     * @return array|null
     */
    public function buscarPorId($id) {
        try {
            $id = $this->conn->real_escape_string($id);
            
            $query = "SELECT id_usuario, nome_completo, usuario, data_criacao FROM usuarios WHERE id_usuario = '$id'";
            
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Erro ao buscar usuário: " . $this->conn->error);
            }
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lista todos os usuários
     * @return array
     */
    public function listarTodos() {
        try {
            $query = "SELECT id_usuario, nome_completo, usuario, data_criacao FROM usuarios ORDER BY nome_completo";
            
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Erro ao listar usuários: " . $this->conn->error);
            }
            
            $usuarios = [];
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $usuarios[] = $row;
                }
            }
            
            return $usuarios;
        } catch (Exception $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cria um novo usuário
     * @param string $nome Nome completo
     * @param string $usuario Nome de usuário
     * @param string $senha Senha do usuário
     * @return bool
     */
    public function criar($nome, $usuario, $senha) {
        try {
            $nome = $this->conn->real_escape_string($nome);
            $usuario = $this->conn->real_escape_string($usuario);
            
            // Hash da senha para armazenamento seguro
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO usuarios (nome_completo, usuario, senha) 
                     VALUES ('$nome', '$usuario', '$senhaHash')";
            
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Erro ao criar usuário: " . $this->conn->error);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualiza um usuário existente
     * @param int $id ID do usuário
     * @param string $nome Nome completo
     * @param string $usuario Nome de usuário
     * @param string|null $senha Nova senha (opcional)
     * @return bool
     */
    public function atualizar($id, $nome, $usuario, $senha = null) {
        try {
            $id = $this->conn->real_escape_string($id);
            $nome = $this->conn->real_escape_string($nome);
            $usuario = $this->conn->real_escape_string($usuario);
            
            // Se a senha foi fornecida, atualize-a também
            if ($senha !== null) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $query = "UPDATE usuarios 
                         SET nome_completo = '$nome', usuario = '$usuario', senha = '$senhaHash' 
                         WHERE id_usuario = '$id'";
            } else {
                $query = "UPDATE usuarios 
                         SET nome_completo = '$nome', usuario = '$usuario' 
                         WHERE id_usuario = '$id'";
            }
            
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Erro ao atualizar usuário: " . $this->conn->error);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exclui um usuário
     * @param int $id ID do usuário
     * @return bool
     */
    public function excluir($id) {
        try {
            $id = $this->conn->real_escape_string($id);
            
            $query = "DELETE FROM usuarios WHERE id_usuario = '$id'";
            
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Erro ao excluir usuário: " . $this->conn->error);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            return false;
        }
    }
}