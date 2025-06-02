<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$base_path = dirname(dirname($_SERVER['SCRIPT_NAME']));
$url_img = rtrim($base_path, '/') . '/img/';
$dir_img = dirname(dirname(__FILE__)) . '/img/';

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

if (!file_exists('includes/header.php')) { 
    echo '<div style="color: red; font-weight: bold; padding: 20px;">
            Erro: O arquivo includes/header.php não foi encontrado!
            <br>Verifique se a estrutura de diretórios está correta.
          </div>';
    exit;
}

require_once 'includes/header.php'; 
require_once '../api/v1/config/database.php'; 

if (!file_exists('includes/Arvore.php')) { 
    echo '<div class="alert alert-danger">
            Erro: O arquivo includes/Arvore.php não foi encontrado!
            <br>Verifique se a estrutura de diretórios está correta.
          </div>';
    require_once 'includes/footer.php'; 
    exit;
}

require_once 'includes/Arvore.php'; 


try {
    $db = getConnection();
    $arvores = []; 
    
    if ($db->connect_error) { 
        throw new Exception("Erro de conexão com o banco: " . $db->connect_error); 
    }
    
    $arvoreModel = new Arvore($db); 
    
    $arvores = $arvoreModel->listarTodas(); 
    
    function ajustarCaminhoImagem($imagem, $url_img_param) {
        if (empty($imagem)) return $url_img_param . 'placeholder.png';
        if (strpos($imagem, 'http://') === 0 || strpos($imagem, 'https://') === 0) { 
            return $imagem; 
        }
        return $url_img_param . $imagem; 
    }
    
    foreach ($arvores as &$arvore_item) {
        if (isset($arvore_item['imagem'])) { 
            $arvore_item['imagem'] = ajustarCaminhoImagem($arvore_item['imagem'], $url_img); 
        } else {
            $arvore_item['imagem'] = ajustarCaminhoImagem(null, $url_img);
        }
    }
    unset($arvore_item); 
    
    $sucesso = isset($_SESSION['arvore_sucesso']) ? $_SESSION['arvore_sucesso'] : ''; 
    $erro = isset($_SESSION['arvore_erro']) ? $_SESSION['arvore_erro'] : ''; 
    
    unset($_SESSION['arvore_sucesso']); 
    unset($_SESSION['arvore_erro']); 

} catch (Exception $e) {
    echo '<div class="alert alert-danger">
            Erro: ' . $e->getMessage() . '
          </div>'; 
    $arvores = []; 
    $sucesso = ''; 
    $erro = 'Erro Crítico: ' . $e->getMessage();
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
                            <td><?php echo htmlspecialchars($arvore['familia'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($arvore['genero'] ?? 'N/A'); ?></td>
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
                        <label for="nome_cientifico" class="form-label">Nome Científico <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome_cientifico" name="nome_cientifico" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="familia" class="form-label">Família</label>
                            <input type="text" class="form-control" id="familia" name="familia">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="genero" class="form-label">Gênero</label>
                            <input type="text" class="form-control" id="genero" name="genero">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nomes_populares" class="form-label">Nomes Populares (separados por vírgula)</label>
                        <input type="text" class="form-control" id="nomes_populares" name="nomes_populares_str" placeholder="Ex: Ipê Amarelo, Pau D'arco">
                    </div>

                    <div class="mb-3">
                        <label for="curiosidade" class="form-label">Curiosidade</label>
                        <textarea class="form-control" id="curiosidade" name="curiosidade" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="imagem_upload" class="form-label">Imagem da Árvore</label>
                        <input type="file" class="form-control" id="imagem_upload" name="imagem_upload" accept="image/*">
                        <div class="form-text">
                            Selecione uma imagem (JPG, PNG, GIF - máx 5MB). Será salva em <code><?php echo htmlspecialchars($dir_img); ?></code>
                        </div>
                    </div>

                    <hr>
                    <h5><i class="bi bi-rulers"></i> Medidas</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="medidas_CAP" class="form-label">CAP (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="medidas_CAP" name="medidas[CAP]" placeholder="Ex: 30.5">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="medidas_DAP" class="form-label">DAP (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="medidas_DAP" name="medidas[DAP]" placeholder="Ex: 15.2">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="medidas_armotizacao" class="form-label">Amortização</label>
                            <input type="text" class="form-control" id="medidas_armotizacao" name="medidas[armotizacao]" placeholder="Ex: Baixa">
                        </div>
                    </div>

                    <hr>
                    <h5><i class="bi bi-tree-fill"></i> Tipo de Árvore</h5>
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3">
                            <label for="tipo_exotica_nativa" class="form-label">Origem</label>
                            <select class="form-select" id="tipo_exotica_nativa" name="tipo_arvore[exotica_nativa]">
                                <option value="" selected>Selecione...</option>
                                <option value="0">Nativa</option>
                                <option value="1">Exótica</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="tipo_medicinal" name="tipo_arvore[medicinal]" value="1">
                                <label class="form-check-label" for="tipo_medicinal">É Medicinal?</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="tipo_toxica" name="tipo_arvore[toxica]" value="1">
                                <label class="form-check-label" for="tipo_toxica">É Tóxica?</label>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h5><i class="bi bi-globe-americas"></i> Biomas</h5>
                    <div class="mb-3">
                        <label for="biomas_nomes_str" class="form-label">Biomas (separados por vírgula)</label>
                        <input type="text" class="form-control" id="biomas_nomes_str" name="biomas_nomes_str" placeholder="Ex: Cerrado, Mata Atlântica">
                        <div class="form-text">Informe os biomas onde a árvore é encontrada. Novos biomas serão cadastrados automaticamente se não existirem.</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Árvore</button>
                </div>
            </form>
        </div>
    </div>
</div>


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
                        <label for="editar_nome_cientifico" class="form-label">Nome Científico <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editar_nome_cientifico" name="nome_cientifico" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editar_familia" class="form-label">Família</label>
                            <input type="text" class="form-control" id="editar_familia" name="familia">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editar_genero" class="form-label">Gênero</label>
                            <input type="text" class="form-control" id="editar_genero" name="genero">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editar_nomes_populares" class="form-label">Nomes Populares (separados por vírgula)</label>
                        <input type="text" class="form-control" id="editar_nomes_populares" name="nomes_populares_str">
                    </div>

                    <div class="mb-3">
                        <label for="editar_curiosidade" class="form-label">Curiosidade</label>
                        <textarea class="form-control" id="editar_curiosidade" name="curiosidade" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagem</label>
                        <div class="mb-2">
                            <div id="editar_imagem_preview_container" class="mb-2" style="max-width: 200px;">
                                <img src="" width="150" class="img-thumbnail" id="imagem_atual_preview_editar" alt="Imagem Atual">
                            </div>
                            <div class="form-text mb-2">Imagem atual: <span id="nome_imagem_atual_editar"></span></div>
                        </div>
                        <label for="editar_imagem_upload" class="form-label">Alterar Imagem</label>
                        <input type="file" class="form-control" id="editar_imagem_upload" name="imagem_upload" accept="image/*">
                        <div class="form-text">
                            Selecione uma nova imagem para substituir. Deixe em branco para manter a atual.
                        </div>
                    </div>

                    <hr>
                    <h5><i class="bi bi-rulers"></i> Medidas</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="editar_medidas_CAP" class="form-label">CAP (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="editar_medidas_CAP" name="medidas[CAP]">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editar_medidas_DAP" class="form-label">DAP (cm)</label>
                            <input type="number" step="0.01" class="form-control" id="editar_medidas_DAP" name="medidas[DAP]">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editar_medidas_armotizacao" class="form-label">Amortização</label>
                            <input type="text" class="form-control" id="editar_medidas_armotizacao" name="medidas[armotizacao]">
                        </div>
                    </div>

                    <hr>
                    <h5><i class="bi bi-tree-fill"></i> Tipo de Árvore</h5>
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3">
                            <label for="editar_tipo_exotica_nativa" class="form-label">Origem</label>
                            <select class="form-select" id="editar_tipo_exotica_nativa" name="tipo_arvore[exotica_nativa]">
                                <option value="">Selecione...</option>
                                <option value="0">Nativa</option>
                                <option value="1">Exótica</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 pt-3">
                             <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="editar_tipo_medicinal" name="tipo_arvore[medicinal]" value="1">
                                <label class="form-check-label" for="editar_tipo_medicinal">É Medicinal?</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="editar_tipo_toxica" name="tipo_arvore[toxica]" value="1">
                                <label class="form-check-label" for="editar_tipo_toxica">É Tóxica?</label>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h5><i class="bi bi-globe-americas"></i> Biomas</h5>
                    <div class="mb-3">
                        <label for="editar_biomas_nomes_str" class="form-label">Biomas (separados por vírgula)</label>
                        <input type="text" class="form-control" id="editar_biomas_nomes_str" name="biomas_nomes_str" placeholder="Ex: Cerrado, Mata Atlântica">
                        <div class="form-text">Informe os biomas onde a árvore é encontrada. Novos biomas serão cadastrados automaticamente se não existirem.</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Árvore</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalExcluirArvore" tabindex="-1" aria-labelledby="modalExcluirArvoreLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExcluirArvoreLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a árvore <strong id="excluir_nome_arvore"></strong>?</p>
                <p class="text-danger">Atenção: Esta ação não poderá ser desfeita e removerá todos os dados associados.</p>
            </div>
            <form action="processa_arvore.php" method="post">
                <input type="hidden" name="acao" value="excluir">
                <input type="hidden" name="id" id="excluir_id_arvore">
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
    const urlImgBase = '<?php echo $url_img; ?>'; 

    const modalNovaArvore = document.getElementById('modalNovaArvore');
    modalNovaArvore.addEventListener('hidden.bs.modal', function () {
        this.querySelector('form').reset();
    });

    const botoesEditar = document.querySelectorAll('.editar-arvore');
    botoesEditar.forEach(botao => {
        botao.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch('processa_arvore.php?acao=buscar&id=' + id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na requisição: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'sucesso' && data.arvore) {
                        const arvore = data.arvore;
                        
                        document.getElementById('editar_id').value = arvore.id;
                        document.getElementById('editar_nome_cientifico').value = arvore.nome_cientifico || '';
                        document.getElementById('editar_familia').value = arvore.familia || '';
                        document.getElementById('editar_genero').value = arvore.genero || '';
                        document.getElementById('editar_curiosidade').value = arvore.curiosidade || '';
                        
                        document.getElementById('editar_nomes_populares').value = arvore.nomes_populares ? arvore.nomes_populares.join(', ') : '';
                        
                        document.getElementById('editar_biomas_nomes_str').value = arvore.biomas_nomes ? arvore.biomas_nomes.join(', ') : '';


                        document.getElementById('editar_imagem_atual').value = arvore.imagem || '';
                        document.getElementById('nome_imagem_atual_editar').textContent = arvore.imagem || 'Nenhuma imagem';
                        const imgPreviewEditar = document.getElementById('imagem_atual_preview_editar');
                        let imagemUrlEditar = '';
                        if (arvore.imagem) {
                            imagemUrlEditar = arvore.imagem.startsWith('http') ? arvore.imagem : urlImgBase + arvore.imagem;
                            imgPreviewEditar.src = imagemUrlEditar;
                            imgPreviewEditar.style.display = 'block';
                        } else {
                            imgPreviewEditar.src = '';
                            imgPreviewEditar.style.display = 'none';
                        }
                        
                        const editarImagemUpload = document.getElementById('editar_imagem_upload');
                        if (editarImagemUpload) {
                            editarImagemUpload.value = ''; 
                        }

                        document.getElementById('editar_medidas_CAP').value = arvore.medidas && arvore.medidas.CAP !== null ? arvore.medidas.CAP : '';
                        document.getElementById('editar_medidas_DAP').value = arvore.medidas && arvore.medidas.DAP !== null ? arvore.medidas.DAP : '';
                        document.getElementById('editar_medidas_armotizacao').value = arvore.medidas && arvore.medidas.armotizacao !== null ? arvore.medidas.armotizacao : '';

                        document.getElementById('editar_tipo_exotica_nativa').value = arvore.tipo_arvore && arvore.tipo_arvore.exotica_nativa !== null ? arvore.tipo_arvore.exotica_nativa : '';
                        document.getElementById('editar_tipo_medicinal').checked = !!(arvore.tipo_arvore && parseInt(arvore.tipo_arvore.medicinal) === 1);
                        document.getElementById('editar_tipo_toxica').checked = !!(arvore.tipo_arvore && parseInt(arvore.tipo_arvore.toxica) === 1);


                    } else {
                        alert('Erro ao buscar dados da árvore: ' + (data.mensagem || 'Resposta inválida do servidor. Verifique o console para detalhes.'));
                        console.error("Detalhes do erro:", data);
                    }
                })
                .catch(error => {
                    console.error('Erro no fetch:', error);
                    alert('Erro de comunicação ao buscar dados da árvore. Tente novamente. Verifique o console para detalhes.');
                });
        });
    });
    
    const botoesExcluir = document.querySelectorAll('.excluir-arvore');
    botoesExcluir.forEach(botao => {
        botao.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nome = this.getAttribute('data-nome'); 
            
            document.getElementById('excluir_id_arvore').value = id;
            document.getElementById('excluir_nome_arvore').textContent = nome;
        });
    });
});
</script>

<?php
require_once 'includes/footer.php'; 
?>