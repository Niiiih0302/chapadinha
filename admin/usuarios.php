<?php
require_once 'includes/header.php';
require_once '../api/v1/config/database.php';
require_once 'includes/Usuario.php';

try {
    $db = getConnection();
    
    $usuarios = [];
    
    if ($db->connect_error) {
        throw new Exception("Erro de conexão com o banco: " . $db->connect_error);
    }
    
    $usuarioModel = new Usuario($db);
    
    $usuarios = $usuarioModel->listarTodos();
    
    $sucesso = isset($_SESSION['usuario_sucesso']) ? $_SESSION['usuario_sucesso'] : '';
    $erro = isset($_SESSION['usuario_erro']) ? $_SESSION['usuario_erro'] : '';
    
    unset($_SESSION['usuario_sucesso']);
    unset($_SESSION['usuario_erro']);
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
    $usuarios = [];
    $sucesso = '';
    $erro = '';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gerenciar Usuários</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoUsuario">
            <i class="bi bi-plus-circle"></i> Novo Usuário
        </button>
    </div>
</div>

<?php if (!empty($sucesso)): ?>
    <div class="alert alert-success" role="alert">
        <?php echo $sucesso; ?>
    </div>
<?php endif; ?>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $erro; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome Completo</th>
                        <th>Usuário</th>
                        <th>Data de Criação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_array($usuarios) && count($usuarios) > 0): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id_usuario']; ?></td>
                            <td><?php echo htmlspecialchars($usuario['nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($usuario['data_criacao'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary editar-usuario" 
                                        data-id="<?php echo $usuario['id_usuario']; ?>"
                                        data-nome="<?php echo htmlspecialchars($usuario['nome_completo']); ?>"
                                        data-usuario="<?php echo htmlspecialchars($usuario['usuario']); ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarUsuario">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger excluir-usuario"
                                        data-id="<?php echo $usuario['id_usuario']; ?>"
                                        data-nome="<?php echo htmlspecialchars($usuario['nome_completo']); ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalExcluirUsuario">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNovoUsuario" tabindex="-1" aria-labelledby="modalNovoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNovoUsuarioLabel">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="processa_usuario.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="cadastrar">
                    
                    <div class="mb-3">
                        <label for="nome_completo" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome_completo" name="nome_completo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                        <div class="form-text">Nome de usuário para login (apenas letras, números e underscore).</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="processa_usuario.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="editar">
                    <input type="hidden" name="id_usuario" id="editar_id">
                    
                    <div class="mb-3">
                        <label for="editar_nome_completo" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="editar_nome_completo" name="nome_completo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editar_usuario" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="editar_usuario" name="usuario" required>
                        <div class="form-text">Nome de usuário para login (apenas letras, números e underscore).</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editar_senha" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" id="editar_senha" name="senha">
                        <div class="form-text">Deixe em branco para manter a senha atual.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editar_confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="editar_confirmar_senha" name="confirmar_senha">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluirUsuario" tabindex="-1" aria-labelledby="modalExcluirUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExcluirUsuarioLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o usuário <strong id="excluir_nome"></strong>?</p>
                <p class="text-danger">Atenção: Esta ação não poderá ser desfeita.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i> Não é possível excluir o usuário atualmente logado.
                </div>
            </div>
            <form action="processa_usuario.php" method="post">
                <input type="hidden" name="acao" value="excluir">
                <input type="hidden" name="id_usuario" id="excluir_id">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const botoesEditar = document.querySelectorAll('.editar-usuario');
    botoesEditar.forEach(botao => {
        botao.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nome = this.getAttribute('data-nome');
            const usuario = this.getAttribute('data-usuario');
            
            document.getElementById('editar_id').value = id;
            document.getElementById('editar_nome_completo').value = nome;
            document.getElementById('editar_usuario').value = usuario;
            
            document.getElementById('editar_senha').value = '';
            document.getElementById('editar_confirmar_senha').value = '';
        });
    });
    
    const botoesExcluir = document.querySelectorAll('.excluir-usuario');
    botoesExcluir.forEach(botao => {
        botao.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nome = this.getAttribute('data-nome');
            
            document.getElementById('excluir_id').value = id;
            document.getElementById('excluir_nome').textContent = nome;
            
            const usuarioLogadoId = <?php echo $_SESSION['usuario_id'] ?? 0; ?>;
            const botaoExcluir = document.querySelector('#modalExcluirUsuario .btn-danger');
            
            if (parseInt(id) === usuarioLogadoId) {
                botaoExcluir.disabled = true;
                botaoExcluir.title = 'Não é possível excluir o usuário atualmente logado';
            } else {
                botaoExcluir.disabled = false;
                botaoExcluir.title = '';
            }
        });
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>