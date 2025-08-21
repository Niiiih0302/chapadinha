<?php
$itemId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '1'; 
$itemType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'arvore'; 

include '../includes/conexao.php';

$itemData = [];
$pageTitle = 'Detalhes do Item';
$url_img = '/chapadinha/img/';

if ($itemType === 'arvore') {
    // ALTERADO: Consulta principal simplificada para a nova estrutura.
    // Removido o JOIN com a tabela 'medidas' e a busca pela coluna 'imagem'.
    $stmt_main = $conn->prepare("
        SELECT 
            a.id,
            a.nome_cientifico,
            a.familia,
            a.genero,
            a.curiosidade,
            t.exotica_nativa,
            t.medicinal,
            t.toxica
        FROM arvore a
        LEFT JOIN tipo_arvore t ON t.fk_arvore = a.id
        WHERE a.id = ?;
    ");

    if (!$stmt_main) {
        die("Erro na preparação da consulta principal: " . $conn->error);
    }

    $stmt_main->bind_param("i", $itemId);
    $stmt_main->execute();
    $resultado_main = $stmt_main->get_result();
    $itemData = $resultado_main->fetch_assoc();
    $stmt_main->close();

    if ($itemData) {
        // A busca por nomes populares permanece a mesma.
        $stmt_np = $conn->prepare("SELECT nome FROM nome_popular WHERE fk_arvore = ?");
        if (!$stmt_np) {
            die("Erro na preparação da consulta de nomes populares: " . $conn->error);
        }
        $stmt_np->bind_param("i", $itemId);
        $stmt_np->execute();
        $resultado_np = $stmt_np->get_result();
        $nomes_populares_arr = [];
        while ($row_np = $resultado_np->fetch_assoc()) {
            $nomes_populares_arr[] = $row_np['nome'];
        }
        $itemData['nomes_populares'] = implode(', ', $nomes_populares_arr);
        $stmt_np->close();

        // NOVO: Buscar imagens da nova tabela 'arvore_imagens'.
        $stmt_img = $conn->prepare("SELECT caminho_imagem FROM arvore_imagens WHERE fk_arvore = ?");
        if (!$stmt_img) {
            die("Erro na preparação da consulta de imagens: " . $conn->error);
        }
        $stmt_img->bind_param("i", $itemId);
        $stmt_img->execute();
        $resultado_img = $stmt_img->get_result();
        $imagens_arr = [];
        while ($row_img = $resultado_img->fetch_assoc()) {
            $imagens_arr[] = $row_img['caminho_imagem'];
        }
        $itemData['imagens'] = $imagens_arr; // Adiciona um array de imagens aos dados.
        $stmt_img->close();


        // REMOVIDO: A busca por biomas foi removida pois as tabelas não existem na nova estrutura.

        // Lógica para definir o título e a descrição permanece.
        $pageTitle = !empty($nomes_populares_arr) ? $nomes_populares_arr[0] : $itemData['nome_cientifico'];

        $tipo = '';
        if (isset($itemData['exotica_nativa'])) {
            $tipo = ($itemData['exotica_nativa'] == 1) ? "exótica" : "nativa";
        } else {
            $tipo = "Não informado";
        }

        $medicinal = (isset($itemData['medicinal']) && $itemData['medicinal'] == 1) ? "medicinal" : "não medicinal";
        $toxica = (isset($itemData['toxica']) && $itemData['toxica'] == 1) ? "tóxica" : "não tóxica";

        $descricao = "Árvore $tipo, $medicinal e $toxica.";
    }
} elseif ($itemType === 'cupim') {
    $itemData = [
        'id' => $itemId,
        'nome_cientifico' => 'Termitidae Structuris',
        'nome_popular' => 'Cupinzeiro da Chapadinha',
        'familia' => 'Termitidae',
        'genero' => 'Cornitermes',
        'curiosidade' => 'Os cupinzeiros são ecossistemas em miniatura, abrigando não apenas cupins, mas também outros insetos e até pequenos vertebrados. A estrutura interna é complexa, com túneis e câmaras para ventilação, cultivo de fungos e armazenamento de alimentos. Eles são essenciais para a ciclagem de nutrientes no solo.',
        'imagens' => ['cupim-card.png'], // ALTERADO: para manter consistência com o array de imagens
        'habitat' => 'Campos abertos e bordas de mata',
        'dieta' => 'Material vegetal em decomposição, celulose',
        'importancia_ecologica' => 'Decomposição de matéria orgânica, aeração do solo, fonte de alimento.'
    ];
    $pageTitle = $itemData['nome_popular'];
} elseif ($itemType === 'lago') {
    $itemData = [
        'id' => $itemId,
        'nome_popular' => 'Lagoa da Chapadinha',
        'curiosidade' => 'A Lagoa da Chapadinha é um ponto central da vida selvagem local, oferecendo recursos hídricos vitais e um habitat diversificado. Sua conservação é crucial para manter o equilíbrio ecológico da região. Atividades de educação ambiental são frequentemente realizadas em suas margens para conscientizar a população sobre sua importância.', // ALTERADO: movido para 'curiosidade'
        'imagens' => ['lago-card.png'], // ALTERADO: para manter consistência com o array de imagens
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
                    <img src="<?php echo $url_img . (!empty($itemData['imagens']) ? htmlspecialchars($itemData['imagens'][0]) : 'placeholder-arvore.png'); ?>" alt="Imagem de <?php echo htmlspecialchars($pageTitle); ?>" class="img-fluid rounded">
                </div>

                <div class="detalhes-curiosidades">
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
</body>

</html>