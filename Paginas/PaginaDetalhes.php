<?php
$itemId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '1'; 
$itemType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'arvore';

include '../includes/conexao.php';

$itemData = [];
$pageTitle = 'Detalhes do Item';
$url_img = '/chapadinha/img/';

if ($itemType === 'arvore') {
    $stmt_main = $conn->prepare("
        SELECT 
            a.id, a.nome_cientifico, a.familia, a.genero, a.curiosidade, a.imagem,
            t.exotica_nativa, t.medicinal, t.toxica
        FROM arvore a
        LEFT JOIN tipo_arvore t ON t.fk_arvore = a.id
        WHERE a.id = ?;
    ");

    if (!$stmt_main) {
        die("Erro na preparação da consulta de árvore: " . $conn->error);
    }

    $stmt_main->bind_param("i", $itemId);
    $stmt_main->execute();
    $resultado_main = $stmt_main->get_result();
    $itemData = $resultado_main->fetch_assoc();
    $stmt_main->close();

    if ($itemData) {
        $stmt_np = $conn->prepare("SELECT nome FROM nome_popular WHERE fk_arvore = ?");
        $stmt_np->bind_param("i", $itemId);
        $stmt_np->execute();
        $resultado_np = $stmt_np->get_result();
        $nomes_populares_arr = [];
        while ($row_np = $resultado_np->fetch_assoc()) {
            $nomes_populares_arr[] = $row_np['nome'];
        }
        $itemData['nomes_populares'] = implode(', ', $nomes_populares_arr);
        $stmt_np->close();

        $pageTitle = !empty($nomes_populares_arr) ? $nomes_populares_arr[0] : $itemData['nome_cientifico'];
    }

} elseif ($itemType === 'cupim') {
    $stmt = $conn->prepare("SELECT * FROM cupinzeiro WHERE id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $itemData = $result->fetch_assoc();
    if ($itemData) {
        $pageTitle = $itemData['nome_popular'];
    }
    $stmt->close();

} elseif ($itemType === 'lagoa') {
    $stmt = $conn->prepare("SELECT * FROM lagoa WHERE id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $itemData = $result->fetch_assoc();
    if ($itemData) {
        $pageTitle = $itemData['nome_popular'];
    }
    $stmt->close();
}

include '../includes/head.php';
?>
<link rel="stylesheet" href="../Estilos/PaginaDetalhesEstilo.css">
<title><?php echo htmlspecialchars($pageTitle); ?> - Lagoa da Chapadinha</title>

<body>
    <?php include '../includes/header.php';  ?>

    <main class="container my-5">
        <?php if (!empty($itemData)): ?>
            <div class="detalhes-container shadow-lg">
                <div class="detalhes-imagem-wrapper">
                    <img src="<?php echo $url_img . htmlspecialchars($itemData['imagem']); ?>" alt="Imagem de <?php echo htmlspecialchars($pageTitle); ?>" class="img-fluid rounded">
                </div>

                <div class="detalhes-curiosidades">
                    <h1 class="nome-item mb-3"><?php echo htmlspecialchars($pageTitle); ?></h1>
                    <?php if ($itemType === 'lagoa'): ?>
                        <h3 class="curiosidades-titulo">Descrição Geral:</h3>
                        <p class="text-indent curiosidades-texto"><?php echo nl2br(htmlspecialchars($itemData['descricao_geral'] ?? 'Nenhuma descrição informada.')); ?></p>
                    <?php else: ?>
                        <h3 class="curiosidades-titulo">Curiosidades:</h3>
                        <p class="text-indent curiosidades-texto"><?php echo nl2br(htmlspecialchars($itemData['curiosidade'] ?? 'Nenhuma curiosidade informada.')); ?></p>
                    <?php endif; ?>
                </div>

                <div class="detalhes-info">
                    <?php if ($itemType === 'arvore'): ?>
                        <p><strong>Nome Científico:</strong> <em><?php echo htmlspecialchars($itemData['nome_cientifico']); ?></em></p>
                        <?php if (!empty($itemData['nomes_populares'])): ?>
                            <p><strong>Nomes Populares:</strong> <?php echo htmlspecialchars($itemData['nomes_populares']); ?>.</p>
                        <?php endif; ?>
                        <p><strong>Família:</strong> <?php echo htmlspecialchars($itemData['familia'] ?? 'N/A'); ?></p>
                        <p><strong>Gênero:</strong> <?php echo htmlspecialchars($itemData['genero'] ?? 'N/A'); ?></p>
                        <?php
                            $tipo = isset($itemData['exotica_nativa']) ? ($itemData['exotica_nativa'] == 1 ? "Exótica" : "Nativa") : "Não informado";
                            $medicinal = isset($itemData['medicinal']) && $itemData['medicinal'] == 1 ? "Medicinal" : "Não medicinal";
                            $toxica = isset($itemData['toxica']) && $itemData['toxica'] == 1 ? "Tóxica" : "Não tóxica";
                        ?>
                        <p><strong>Tipo:</strong> <?php echo "$tipo, $medicinal e $toxica."; ?></p>

                    <?php elseif ($itemType === 'cupim'): ?>
                        <p><strong>Nome Científico:</strong> <em><?php echo htmlspecialchars($itemData['nome_cientifico']); ?></em></p>
                        <p><strong>Família:</strong> <?php echo htmlspecialchars($itemData['familia'] ?? 'N/A'); ?></p>
                        <p><strong>Gênero:</strong> <?php echo htmlspecialchars($itemData['genero'] ?? 'N/A'); ?></p>
                        <p><strong>Habitat:</strong> <?php echo htmlspecialchars($itemData['habitat'] ?? 'N/A'); ?></p>
                        <p><strong>Dieta:</strong> <?php echo htmlspecialchars($itemData['dieta'] ?? 'N/A'); ?></p>
                        <p><strong>Importância Ecológica:</strong> <?php echo htmlspecialchars($itemData['importancia_ecologica'] ?? 'N/A'); ?></p>
                    
                    <?php elseif ($itemType === 'lagoa'): ?>
                        <p><strong>Localização:</strong> <?php echo htmlspecialchars($itemData['localizacao'] ?? 'N/A'); ?></p>
                        <p><strong>Tipo de Água:</strong> <?php echo htmlspecialchars($itemData['tipo_agua'] ?? 'N/A'); ?></p>
                        <p><strong>Fauna em Destaque:</strong> <?php echo htmlspecialchars($itemData['fauna_destaque'] ?? 'N/A'); ?></p>
                        <p><strong>Flora em Destaque:</strong> <?php echo htmlspecialchars($itemData['flora_destaque'] ?? 'N/A'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="mt-4 pt-3 border-top w-100">
                    <a href="PaginaCards.php" class="btn btn-outline-primary"> <i class="bi bi-arrow-left-circle"></i> Ver Outros Itens</a>
                    <a href="index.php" class="btn btn-outline-secondary ms-2"> <i class="bi bi-house"></i> Voltar para Início</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                Oops! As informações para este item não foram encontradas ou o item não existe.
            </div>
            <div class="text-center mt-3">
                <a href="PaginaCards.php" class="btn btn-primary"> <i class="bi bi-arrow-left-circle"></i> Voltar ao Catálogo</a>
            </div>
        <?php endif; ?>
    </main>

    <?php 
    $conn->close();
    include '../includes/footer.php'; 
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>