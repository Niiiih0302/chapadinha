<?php
$itemId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '1';
$itemType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'arvore';

include '../includes/conexao.php';

$itemData = [];
$pageTitle = 'Detalhes do Item';
$url_img = '/chapadinha/img/';

if ($itemType === 'arvore') {
    // ... (toda a sua lógica PHP para buscar dados permanece a mesma) ...
    $stmt_main = $conn->prepare("
        SELECT
            a.id, a.nome_cientifico, a.familia, a.genero, a.curiosidade,
            t.exotica_nativa, t.medicinal, t.toxica
        FROM arvore a
        LEFT JOIN tipo_arvore t ON t.fk_arvore = a.id
        WHERE a.id = ?;
    ");
    if (!$stmt_main) { die("Erro na preparação da consulta principal: " . $conn->error); }
    $stmt_main->bind_param("i", $itemId);
    $stmt_main->execute();
    $resultado_main = $stmt_main->get_result();
    $itemData = $resultado_main->fetch_assoc();
    $stmt_main->close();

    if ($itemData) {
        $stmt_np = $conn->prepare("SELECT nome FROM nome_popular WHERE fk_arvore = ?");
        if (!$stmt_np) { die("Erro na preparação da consulta de nomes populares: " . $conn->error); }
        $stmt_np->bind_param("i", $itemId);
        $stmt_np->execute();
        $resultado_np = $stmt_np->get_result();
        $nomes_populares_arr = [];
        while ($row_np = $resultado_np->fetch_assoc()) {
            $nomes_populares_arr[] = $row_np['nome'];
        }
        $itemData['nomes_populares'] = implode(', ', $nomes_populares_arr);
        $stmt_np->close();

        $stmt_img = $conn->prepare("SELECT caminho_imagem FROM arvore_imagens WHERE fk_arvore = ?");
        if (!$stmt_img) { die("Erro na preparação da consulta de imagens: " . $conn->error); }
        $stmt_img->bind_param("i", $itemId);
        $stmt_img->execute();
        $resultado_img = $stmt_img->get_result();
        $imagens_arr = [];
        while ($row_img = $resultado_img->fetch_assoc()) {
            $imagens_arr[] = $row_img['caminho_imagem'];
        }
        $itemData['imagens'] = $imagens_arr;
        $stmt_img->close();

        $pageTitle = !empty($nomes_populares_arr) ? $nomes_populares_arr[0] : $itemData['nome_cientifico'];
        $tipo = isset($itemData['exotica_nativa']) ? (($itemData['exotica_nativa'] == 1) ? "exótica" : "nativa") : "Não informado";
        $medicinal = (isset($itemData['medicinal']) && $itemData['medicinal'] == 1) ? "medicinal" : "não medicinal";
        $toxica = (isset($itemData['toxica']) && $itemData['toxica'] == 1) ? "tóxica" : "não tóxica";
        $descricao = "Árvore $tipo, $medicinal e $toxica.";
    }
} elseif ($itemType === 'cupim') {
    // ... (lógica para cupim) ...
     $itemData = [
        'id' => $itemId,
        'nome_cientifico' => 'Termitidae Structuris',
        'nome_popular' => 'Cupinzeiro da Chapadinha',
        'familia' => 'Termitidae',
        'genero' => 'Cornitermes',
        'curiosidade' => 'Os cupinzeiros são ecossistemas em miniatura, abrigando não apenas cupins, mas também outros insetos e até pequenos vertebrados. A estrutura interna é complexa, com túneis e câmaras para ventilação, cultivo de fungos e armazenamento de alimentos. Eles são essenciais para a ciclagem de nutrientes no solo.',
        'imagens' => ['cupim-card.png'],
        'habitat' => 'Campos abertos e bordas de mata',
        'dieta' => 'Material vegetal em decomposição, celulose',
        'importancia_ecologica' => 'Decomposição de matéria orgânica, aeração do solo, fonte de alimento.'
    ];
    $pageTitle = $itemData['nome_popular'];
} elseif ($itemType === 'lago') {
    // ... (lógica para lago) ...
    $itemData = [
        'id' => $itemId,
        'nome_popular' => 'Lagoa da Chapadinha',
        'curiosidade' => 'A Lagoa da Chapadinha é um ponto central da vida selvagem local, oferecendo recursos hídricos vitais e um habitat diversificado. Sua conservação é crucial para manter o equilíbrio ecológico da região. Atividades de educação ambiental são frequentemente realizadas em suas margens para conscientizar a população sobre sua importância.',
        'imagens' => ['lago-card.png'],
        'localizacao' => 'Parque Municipal da Chapadinha',
        'tipo_agua' => 'Doce, com pH neutro',
        'fauna_destaque' => 'Peixes como lambaris e traíras, aves aquáticas como garças e patos-selvagens, além de capivaras.',
        'flora_destaque' => 'Presença de aguapés, taboas e vegetação ciliar nativa que protege as margens contra erosão.'
    ];
    $pageTitle = $itemData['nome_popular'];
}

include '../includes/head.php';
?>
<link rel="stylesheet" href="../Estilos/PaginaDetalhesEstilo.css">
<title><?php echo htmlspecialchars($pageTitle); ?> - Lagoa da Chapadinha</title>

<body>
    <?php include '../includes/header.php'; ?>

    <main class="container my-5">
        <?php if (!empty($itemData)): ?>
            <div class="detalhes-container shadow-lg">
            <div class="detalhes-imagem-wrapper">
    <?php if (!empty($itemData['imagens']) && count($itemData['imagens']) > 0): ?>

        <div id="carouselDetalhesIndicadores" class="carousel slide shadow-sm" data-bs-ride="carousel">

            <?php if (count($itemData['imagens']) > 1): ?>
            <div class="carousel-indicators">
                <?php foreach ($itemData['imagens'] as $index => $imagem): ?>
                    <button type="button" 
                            data-bs-target="#carouselDetalhesIndicadores" 
                            data-bs-slide-to="<?php echo $index; ?>" 
                            class="<?php echo ($index == 0) ? 'active' : ''; ?>" 
                            aria-current="<?php echo ($index == 0) ? 'true' : 'false'; ?>" 
                            aria-label="Slide <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="carousel-inner rounded">
                <?php foreach ($itemData['imagens'] as $index => $imagem):
                    $activeClass = ($index == 0) ? 'active' : '';
                ?>
                <div class="carousel-item <?php echo $activeClass; ?>">
                    <img src="<?php echo $url_img . htmlspecialchars($imagem); ?>" class="d-block w-100" alt="Imagem <?php echo $index + 1; ?> de <?php echo htmlspecialchars($pageTitle); ?>">
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($itemData['imagens']) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselDetalhesIndicadores" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselDetalhesIndicadores" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            <?php endif; ?>

        </div>

    <?php else: ?>
        <img src="<?php echo $url_img . 'placeholder-arvore.png'; ?>" alt="Imagem de <?php echo htmlspecialchars($pageTitle); ?>" class="img-fluid rounded">
    <?php endif; ?>
</div>

                <div class="detalhes-curiosidades">
                    <button class="btn btn-outline-success btn-share" onclick="sharePage()" title="Compartilhar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-share-fill" viewBox="0 0 16 16">
                            <path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.5 2.5 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5"/>
                        </svg>
                    </button>

                    <h1 class="nome-item mb-3"><?php echo htmlspecialchars($pageTitle); ?></h1>

                    <?php if($itemType === 'arvore' || $itemType === 'cupim'): ?>
                        <h3 class="curiosidades-titulo">Curiosidades:</h3>
                        <p class="text-indent curiosidades-texto"><?php echo nl2br(htmlspecialchars($itemData['curiosidade'] ?? 'Nenhuma curiosidade informada.')); ?></p>
                    <?php elseif($itemType === 'lago'): ?>
                        <h3 class="curiosidades-titulo">Descrição Geral:</h3>
                        <p class="text-indent curiosidades-texto"><?php echo nl2br(htmlspecialchars($itemData['curiosidade'])); ?></p>
                    <?php endif; ?>
                </div>

                <div class="detalhes-info">
                    <?php if($itemType === 'arvore'): ?>
                        <p><strong>Nome Científico:</strong> <em><?php echo htmlspecialchars($itemData['nome_cientifico']); ?></em></p>
                        <?php if (!empty($itemData['nomes_populares'])): ?>
                            <p><strong>Nomes Populares:</strong> <?php echo htmlspecialchars($itemData['nomes_populares']); ?>.</p>
                        <?php endif; ?>
                        <p><strong>Família:</strong> <?php echo htmlspecialchars($itemData['familia'] ?? 'N/A'); ?></p>
                        <p><strong>Gênero:</strong> <?php echo htmlspecialchars($itemData['genero'] ?? 'N/A'); ?></p>
                        <p><strong>Tipo:</strong> <?php echo $descricao; ?></p>
                    <?php elseif($itemType === 'cupim'): ?>
                        <p><strong>Nome Científico:</strong> <em><?php echo htmlspecialchars($itemData['nome_cientifico']); ?></em></p>
                        <p><strong>Família:</strong> <?php echo htmlspecialchars($itemData['familia']); ?></p>
                        <p><strong>Gênero:</strong> <?php echo htmlspecialchars($itemData['genero']); ?></p>
                        <p><strong>Habitat:</strong> <?php echo htmlspecialchars($itemData['habitat']); ?></p>
                        <p><strong>Dieta:</strong> <?php echo htmlspecialchars($itemData['dieta']); ?></p>
                        <p><strong>Importância Ecológica:</strong> <?php echo htmlspecialchars($itemData['importancia_ecologica']); ?></p>
                    <?php elseif($itemType === 'lago'): ?>
                        <p><strong>Localização:</strong> <?php echo htmlspecialchars($itemData['localizacao']); ?></p>
                        <p><strong>Tipo de Água:</strong> <?php echo htmlspecialchars($itemData['tipo_agua']); ?></p>
                        <p><strong>Destaques da Fauna:</strong> <?php echo htmlspecialchars($itemData['fauna_destaque']); ?></p>
                        <p><strong>Destaques da Flora:</strong> <?php echo htmlspecialchars($itemData['flora_destaque']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="mt-4 pt-3 border-top w-100">
                    <a href="PaginaCards.php" class="btn btn-outline-primary"> <i class="bi bi-arrow-left-circle"></i> Voltar para o Catálogo</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                Oops! As informações para este item não foram encontradas ou o item não existe.
            </div>
            <div class="text-center mt-3">
                <a href="PaginaCards.php" class="btn btn-primary"> <i class="bi bi-arrow-left-circle"></i> Voltar para o Catálogo</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function sharePage() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                text: 'Veja os detalhes sobre: <?php echo htmlspecialchars($pageTitle, ENT_QUOTES); ?>',
                url: window.location.href
            }).then(() => {
                console.log('Página compartilhada com sucesso!');
            }).catch((error) => {
                console.error('Erro ao compartilhar:', error);
            });
        } else {
            // Fallback para navegadores que não suportam a Web Share API
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link copiado para a área de transferência!');
            }).catch(err => {
                alert('Não foi possível copiar o link. Por favor, copie manualmente a URL.');
                console.error('Erro ao copiar link:', err);
            });
        }
    }
    </script>
</body>
</html>