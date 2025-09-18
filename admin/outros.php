<?php
require_once 'includes/header.php';
require_once '../api/v1/config/database.php';

try {
    $db = getConnection();

    $result_cupim = $db->query("SELECT * FROM cupinzeiro WHERE id = 1");
    $cupim = $result_cupim->fetch_assoc();

    $result_lagoa = $db->query("SELECT * FROM lagoa WHERE id = 1");
    $lagoa = $result_lagoa->fetch_assoc();

} catch (Exception $e) {
    echo '<div class="alert alert-danger">Erro ao conectar ou buscar dados: ' . $e->getMessage() . '</div>';
    $cupim = [];
    $lagoa = [];
}

$sucesso = isset($_SESSION['item_sucesso']) ? $_SESSION['item_sucesso'] : '';
$erro = isset($_SESSION['item_erro']) ? $_SESSION['item_erro'] : '';
unset($_SESSION['item_sucesso']);
unset($_SESSION['item_erro']);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gerenciar Outros Itens</h1>
</div>

<?php if (!empty($sucesso)): ?>
    <div class="alert alert-success" role="alert"><?php echo $sucesso; ?></div>
<?php endif; ?>
<?php if (!empty($erro)): ?>
    <div class="alert alert-danger" role="alert"><?php echo $erro; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-mountain"></i> Editar Cupinzeiro</h3>
            </div>
            <div class="card-body">
                <form action="processa_outros.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="tipo" value="cupim">
                    <div class="mb-3"><label class="form-label">Nome Popular</label><input type="text" name="nome_popular" class="form-control" value="<?php echo htmlspecialchars($cupim['nome_popular'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Nome Científico</label><input type="text" name="nome_cientifico" class="form-control" value="<?php echo htmlspecialchars($cupim['nome_cientifico'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Família</label><input type="text" name="familia" class="form-control" value="<?php echo htmlspecialchars($cupim['familia'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Gênero</label><input type="text" name="genero" class="form-control" value="<?php echo htmlspecialchars($cupim['genero'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Habitat</label><input type="text" name="habitat" class="form-control" value="<?php echo htmlspecialchars($cupim['habitat'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Dieta</label><input type="text" name="dieta" class="form-control" value="<?php echo htmlspecialchars($cupim['dieta'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Importância Ecológica</label><textarea name="importancia_ecologica" class="form-control" rows="3"><?php echo htmlspecialchars($cupim['importancia_ecologica'] ?? ''); ?></textarea></div>
                    <div class="mb-3"><label class="form-label">Curiosidade</label><textarea name="curiosidade" class="form-control" rows="4"><?php echo htmlspecialchars($cupim['curiosidade'] ?? ''); ?></textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Imagem Principal</label><br>
                        <img src="../img/<?php echo htmlspecialchars($cupim['imagem'] ?? 'placeholder.png'); ?>" width="100" class="img-thumbnail mb-2">
                        <input type="file" name="imagem" class="form-control">
                        <div class="form-text">Envie uma nova imagem para substituir a atual.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Alterações do Cupinzeiro</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-water"></i> Editar Lagoa</h3>
            </div>
            <div class="card-body">
                <form action="processa_outros.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="tipo" value="lagoa">
                    <div class="mb-3"><label class="form-label">Nome Popular</label><input type="text" name="nome_popular" class="form-control" value="<?php echo htmlspecialchars($lagoa['nome_popular'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Localização</label><input type="text" name="localizacao" class="form-control" value="<?php echo htmlspecialchars($lagoa['localizacao'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Tipo de Água</label><input type="text" name="tipo_agua" class="form-control" value="<?php echo htmlspecialchars($lagoa['tipo_agua'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Fauna em Destaque</label><textarea name="fauna_destaque" class="form-control" rows="3"><?php echo htmlspecialchars($lagoa['fauna_destaque'] ?? ''); ?></textarea></div>
                    <div class="mb-3"><label class="form-label">Flora em Destaque</label><textarea name="flora_destaque" class="form-control" rows="3"><?php echo htmlspecialchars($lagoa['flora_destaque'] ?? ''); ?></textarea></div>
                    <div class="mb-3"><label class="form-label">Descrição Geral</label><textarea name="descricao_geral" class="form-control" rows="4"><?php echo htmlspecialchars($lagoa['descricao_geral'] ?? ''); ?></textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Imagem Principal</label><br>
                        <img src="../img/<?php echo htmlspecialchars($lagoa['imagem'] ?? 'placeholder.png'); ?>" width="100" class="img-thumbnail mb-2">
                        <input type="file" name="imagem" class="form-control">
                        <div class="form-text">Envie uma nova imagem para substituir a atual.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Alterações da Lagoa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>