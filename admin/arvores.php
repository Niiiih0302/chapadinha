<?php
// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir o caminho relativo para as imagens
$base_path = dirname(dirname($_SERVER['SCRIPT_NAME'])); // Volta um nível (para a raiz do projeto)
$url_img = $base_path . '/img/';
$dir_img = dirname(dirname(__FILE__)) . '/img/'; // Caminho físico para a pasta img

// Verifica se a pasta de imagens existe e tem permissão de escrita
if (!is_dir($dir_img)) {
    echo '<div class="alert alert-danger">
            Erro: A pasta de imagens não existe: ' . htmlspecialchars($dir_img) . '
            <br>Verifique se a estrutura de diretórios está correta.
          </div>';
} elseif (!is_writable($dir_img)) {
    echo '<div class="alert alert-danger">
            Erro: A pasta de imagens não tem permissões de escrita: ' . htmlspecialchars($dir_img) . '
            <br>Verifique as permissões do diretório.
          </div>';
}

// Verifica se o arquivo header.php existe
if (!file_exists('includes/header.php')) {
    echo '<div style="color: red; font-weight: bold; padding: 20px;">
            Erro: O arquivo includes/header.php não foi encontrado!
            <br>Verifique se a estrutura de diretórios está correta.
          </div>';
    exit;
}

// Inclui cabeçalho
require_once 'includes/header.php';

// Inclui arquivos necessários
require_once '../api/v1/config/database.php';

// Verifica se os arquivos necessários existem
if (!file_exists('includes/Arvore.php')) {
    echo '<div class="alert alert-danger">
            Erro: O arquivo includes/Arvore.php não foi encontrado!
            <br>Verifique se a estrutura de diretórios está correta.
          </div>';
    require_once 'includes/footer.php';
    exit;
}

// if (!file_exists('includes/Categoria.php')) {
//     echo '<div class="alert alert-danger">
//             Erro: O arquivo includes/Categoria.php não foi encontrado!
//             <br>Verifique se a estrutura de diretórios está correta.
//           </div>';
//     require_once 'includes/footer.php';
//     exit;
// }

require_once 'includes/Arvore.php';
// require_once 'includes/Categoria.php';

// Conecta ao banco de dados
try {
    $db = getConnection();
    
    // Inicializa as variáveis como arrays vazios para evitar warnings
    $arvores = [];
    // $categorias = [];
    
    if ($db->connect_error) {
        throw new Exception("Erro de conexão com o banco: " . $db->connect_error);
    }
    
    $arvoreModel = new Arvore($db);
    // $categoriaModel = new Categoria($db);
    
    // Busca todas as notícias
    $arvores = $arvoreModel->listarTodas();
    
    // Função para ajustar o caminho da imagem
    function ajustarCaminhoImagem($imagem, $url_img) {
        // Se a imagem já é uma URL completa (começa com http:// ou https://), não modifica
        if (strpos($imagem, 'http://') === 0 || strpos($imagem, 'https://') === 0) {
            return $imagem;
        }
        
        // Se a imagem é apenas o nome do arquivo, assume que está na pasta img
        return $url_img . $imagem;
    }
    
    // Ajusta o caminho das imagens nas notícias
    foreach ($arvores as &$arvore) {
        if (isset($arvore['imagem'])) {
            $arvore['imagem'] = ajustarCaminhoImagem($arvore['imagem'], $url_img);
        }
    }
    unset($arvore); // Importante para evitar problemas com referências
    
    // Busca todas as categorias para o formulário
    // $categorias = $categoriaModel->listarTodas();
    
    // Verifica se há mensagem de sucesso ou erro
    $sucesso = isset($_SESSION['arvore_sucesso']) ? $_SESSION['arvore_sucesso'] : '';
    $erro = isset($_SESSION['arvore_erro']) ? $_SESSION['arvore_erro'] : '';
    
    // Limpa as mensagens
    unset($_SESSION['arvore_sucesso']);
    unset($_SESSION['arvore_erro']);
} catch (Exception $e) {
    echo '<div class="alert alert-danger">
            Erro: ' . $e->getMessage() . '
          </div>';
    $arvore = [];
    $categorias = [];
    $sucesso = '';
    $erro = '';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gerenciar Árvores</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovaArvore">
            <i class="bi bi-plus-circle"></i> Nova Árvore
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
                        <th>Imagem</th>
                        <th>Nome Científico</th>
                        <th>Família</th>
                        <th>Gênero</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_array($arvores) && count($arvores) > 0): ?>
                        <?php foreach ($arvores as $arvore): ?>
                        <tr>
                            <td><?php echo $arvore['id']; ?></td>
                            <td>
                                <?php
                                $imagem_url = htmlspecialchars($arvore['imagem']);
                                $imagem_alt = htmlspecialchars($arvore['nome_cientifico']);
                                ?>
                                <img src="<?php echo $imagem_url; ?>" alt="<?php echo $imagem_alt; ?>" width="50" height="50" class="img-thumbnail">
                            </td>
                            <td><?php echo htmlspecialchars($arvore['nome_cientifico']); ?></td>
                            <td><?php echo htmlspecialchars($arvore['familia']); ?></td>
                            <td><?php echo htmlspecialchars($arvore['genero']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary editar-arvore" 
                                        data-id="<?php echo $arvore['id']; ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarArvore">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger excluir-arvore"
                                        data-id="<?php echo $arvore['id']; ?>"
                                        data-nome="<?php echo htmlspecialchars($arvore['nome_cientifico']); ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalExcluirArvore">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma árvore encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal Nova Notícia -->
<div class="modal fade" id="modalNovaArvore" tabindex="-1" aria-labelledby="modalNovaArvoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNovaArvoreLabel">Nova Árvore</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="processa_arvore.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="cadastrar">
                    
                    <div class="mb-3">
                        <label for="nome_cientifico" class="form-label">Nome Científico</label>
                        <input type="text" class="form-control" id="nome_cientifico" name="nome_cientifico" required>
                    </div>

                    <div class="mb-3">
                        <label for="familia" class="form-label">Família</label>
                        <input type="text" class="form-control" id="familia" name="familia" required>
                    </div>

                    <div class="mb-3">
                        <label for="genero" class="form-label">Gênero</label>
                        <input type="text" class="form-control" id="genero" name="genero" required>
                    </div>

                    <div class="mb-3">
                        <label for="curiosidade" class="form-label">Curiosidade</label>
                        <textarea class="form-control" id="curiosidade" name="curiosidade" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="imagem_upload" class="form-label">Imagem da Árvore</label>
                        <input type="file" class="form-control" id="imagem_upload" name="imagem_upload" accept="image/*" required>
                        <div class="form-text">
                            Selecione uma imagem no formato JPG, PNG ou GIF (máximo 5 MB).
                            <br>A imagem será salva na pasta <code><?php echo htmlspecialchars($url_img); ?></code>
                        </div>
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


<!-- Modal Editar Notícia -->
<div class="modal fade" id="modalEditarArvore" tabindex="-1" aria-labelledby="modalEditarArvoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarArvoreLabel">Editar Árvore</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="processa_arvore.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="editar">
                    <input type="hidden" name="id" id="editar_id">
                    <input type="hidden" name="imagem_atual" id="editar_imagem_atual">

                    <div class="mb-3">
                        <label for="editar_nome_cientifico" class="form-label">Nome Científico</label>
                        <input type="text" class="form-control" id="editar_nome_cientifico" name="nome_cientifico" required>
                    </div>

                    <div class="mb-3">
                        <label for="editar_familia" class="form-label">Família</label>
                        <input type="text" class="form-control" id="editar_familia" name="familia" required>
                    </div>

                    <div class="mb-3">
                        <label for="editar_genero" class="form-label">Gênero</label>
                        <input type="text" class="form-control" id="editar_genero" name="genero" required>
                    </div>

                    <div class="mb-3">
                        <label for="editar_curiosidade" class="form-label">Curiosidade</label>
                        <textarea class="form-control" id="editar_curiosidade" name="curiosidade" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagem</label>
                        <div class="mb-2">
                            <div id="editar_imagem_preview" class="mb-2">
                                <img src="" width="150" class="img-thumbnail" id="imagem_atual_preview">
                            </div>
                            <div class="form-text mb-2">Imagem atual: <span id="nome_imagem_atual"></span></div>
                        </div>

                        <div class="mb-3">
                            <label for="editar_imagem_upload" class="form-label">Alterar Imagem</label>
                            <input type="file" class="form-control" id="editar_imagem_upload" name="imagem_upload" accept="image/*">
                            <div class="form-text">
                                Selecione uma nova imagem no formato JPG, PNG ou GIF (máximo 5 MB).
                                <br>Deixe este campo em branco para manter a imagem atual.
                            </div>
                        </div>
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


<!-- Modal Excluir Notícia -->
<div class="modal fade" id="modalExcluirArvore" tabindex="-1" aria-labelledby="modalExcluirArvoreLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExcluirArvoreLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a árvore <strong id="excluir_nome"></strong>?</p>
                <p class="text-danger">Atenção: Esta ação não poderá ser desfeita.</p>
            </div>
            <form action="processa_arvore.php" method="post">
                <input type="hidden" name="acao" value="excluir">
                <input type="hidden" name="id" id="excluir_id">
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
    // Preenche o modal de edição ao clicar no botão editar
    const botoesEditar = document.querySelectorAll('.editar-arvore');
    botoesEditar.forEach(botao => {
        botao.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            // Fazer requisição AJAX para buscar os dados da notícia
            fetch('processa_arvore.php?acao=buscar&id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.status == 'sucesso') {
                        const arvore = data.arvore;
                        const baseUrl = '<?php echo $url_img; ?>';
                        
                        // Preenche o formulário de edição
                        document.getElementById('editar_id').value = arvore.id;
                        document.getElementById('editar_nome_cientifico').value = arvore.nome_cientifico;
                        document.getElementById('editar_familia').value = arvore.familia;
                        document.getElementById('editar_genero').value = arvore.genero;
                        document.getElementById('editar_curiosidade').value = arvore.curiosidade;
                        
                        // Exibe a imagem atual
                        document.getElementById('editar_imagem_atual').value = arvore.imagem;
                        document.getElementById('nome_imagem_atual').textContent = arvore.imagem;
                        
                        // Prepara a URL da imagem para visualização
                        let imagemUrl = arvore.imagem;
                        if (!imagemUrl.startsWith('http://') && !imagemUrl.startsWith('https://')) {
                            imagemUrl = baseUrl + imagemUrl;
                        }
                        document.getElementById('imagem_atual_preview').src = imagemUrl;
                        
                        // Limpa o campo de upload
                        if (document.getElementById('editar_imagem_upload')) {
                            document.getElementById('editar_imagem_upload').value = '';
                        }
                    } else {
                        alert('Erro ao buscar dados da árvore: ' + data.mensagem);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao buscar dados da árvore. Tente novamente.');
                });
        });
    });
    
    // Preenche o modal de exclusão
    const botoesExcluir = document.querySelectorAll('.excluir-arvore');
    botoesExcluir.forEach(botao => {
        botao.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            document.getElementById('excluir_id').value = id;
            document.getElementById('excluir_nome_cientifico').textContent = nome_cientifico;
        });
    });
});
</script>


<?php
// Inclui rodapé
require_once 'includes/footer.php';
?>
