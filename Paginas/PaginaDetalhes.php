<?php
// Simula a busca de dados com base em um ID e tipo passados via GET.
// Em um cenário real, você obteria esses dados do banco de dados.
$itemId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '1'; // ID padrão
$itemType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'arvore'; // Tipo padrão

include '../includes/conexao.php';

// Dados de exemplo - substitua pela sua lógica de banco de dados
$itemData = [];
$pageTitle = 'Detalhes do Item';
$url_img = '/chapadinha/img/';

if ($itemType === 'arvore') {
    // Consulta principal para dados da árvore, medidas e tipo_arvore
    $stmt_main = $conn->prepare("
        SELECT 
            a.id,
            a.nome_cientifico,
            a.familia,
            a.genero,
            a.curiosidade,
            a.imagem,
            m.CAP,
            m.DAP,
            m.amortizacao, -- CORRIGIDO AQUI: de 'amortizacao' para 'amortizacao'
            t.exotica_nativa,
            t.medicinal,
            t.toxica
        FROM arvore a
        LEFT JOIN medidas m ON m.fk_arvore = a.id
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
        // Buscar nomes populares separadamente
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

        // Buscar biomas separadamente
        $stmt_bioma = $conn->prepare("SELECT b.nome FROM arvore_bioma ab JOIN bioma b ON ab.fk_bioma = b.id WHERE ab.fk_arvore = ?");
        if (!$stmt_bioma) {
            die("Erro na preparação da consulta de biomas: " . $conn->error);
        }
        $stmt_bioma->bind_param("i", $itemId);
        $stmt_bioma->execute();
        $resultado_bioma = $stmt_bioma->get_result();
        $biomas_arr = [];
        while ($row_bioma = $resultado_bioma->fetch_assoc()) {
            $biomas_arr[] = $row_bioma['nome'];
        }
        $itemData['biomas_nomes'] = implode(', ', $biomas_arr); // Armazena como biomas_nomes
        $stmt_bioma->close();

        // Definir pageTitle
        $pageTitle = !empty($nomes_populares_arr) ? $nomes_populares_arr[0] : $itemData['nome_cientifico'];

        // Variáveis para a exibição do tipo da árvore
        $tipo = '';
        if (isset($itemData['exotica_nativa'])) {
            $tipo = ($itemData['exotica_nativa'] == 1) ? "exótica" : "nativa"; // Corrigido para nativa/exótica
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
        'imagem' => '../img/cupim-card.png',
        'habitat' => 'Campos abertos e bordas de mata',
        'dieta' => 'Material vegetal em decomposição, celulose',
        'importancia_ecologica' => 'Decomposição de matéria orgânica, aeração do solo, fonte de alimento.'
    ];
    $pageTitle = $itemData['nome_popular'];
} elseif ($itemType === 'lago') {
    $itemData = [
        'id' => $itemId,
        'nome_popular' => 'Lagoa da Chapadinha',
        'descricao_geral' => 'A Lagoa da Chapadinha é um ponto central da vida selvagem local, oferecendo recursos hídricos vitais e um habitat diversificado. Sua conservação é crucial para manter o equilíbrio ecológico da região. Atividades de educação ambiental são frequentemente realizadas em suas margens para conscientizar a população sobre sua importância.',
        'imagem' => '../img/lago-card.png',
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
    <?php include '../includes/header.php';  ?>

    <main class="container my-5">
        <?php if (!empty($itemData)): ?>
            <div class="detalhes-container shadow-lg">
                <div class="detalhes-imagem-wrapper">
                    <img src="<?php echo $url_img . $itemData['imagem']; ?>" alt="Imagem de <?php echo htmlspecialchars($pageTitle); ?>" class="img-fluid rounded">
                </div>

                <div class="detalhes-curiosidades">
                    <h1 class="nome-item mb-3"><?php echo htmlspecialchars($pageTitle); ?></h1>
                    <h3 class="curiosidades-titulo">Curiosidades:</h3>
                    <p class="text-indent curiosidades-texto"><?php echo nl2br(htmlspecialchars($itemData['curiosidade'] ?? 'Nenhuma curiosidade informada.')); ?></p>
                </div>

                <div class="detalhes-info">
                    <p><strong>Nome Científico:</strong> <em><?php echo htmlspecialchars($itemData['nome_cientifico']); ?></em></p>
                    <?php if (!empty($itemData['nomes_populares'])): ?>
                        <p><strong>Nomes Populares:</strong> <?php echo htmlspecialchars($itemData['nomes_populares']); ?>.</p>
                    <?php endif; ?>
                    <p><strong>Família:</strong> <?php echo htmlspecialchars($itemData['familia'] ?? 'N/A'); ?></p>
                    <p><strong>Gênero:</strong> <?php echo htmlspecialchars($itemData['genero'] ?? 'N/A'); ?></p>
                    <p><strong>Biomas:</strong> <?php echo htmlspecialchars($itemData['biomas_nomes'] ?? 'N/A'); ?></p>
                    <p><strong>Tipo:</strong> <?php echo $descricao; ?></p>
                    <p><strong>Medidas Comuns:</strong></p>
                    <p>CAP: <?php echo htmlspecialchars($itemData['CAP'] ?? 'N/A'); ?></p>
                    <p>DAP: <?php echo htmlspecialchars($itemData['DAP'] ?? 'N/A'); ?></p>
                    <p>Amortização de carbono: <?php echo htmlspecialchars($itemData['amortizacao'] ?? 'N/A'); ?></p>
                </div>

                <div class="mt-4 pt-3 border-top w-100">
                    <a href="PaginaCards.php" class="btn btn-outline-primary"> <i class="bi bi-arrow-left-circle"></i> Ver Outros Itens</a>
                    <a href="index.php" class="btn btn-outline-secondary ms-2"> <i class="bi bi-house"></i> Voltar para Início</a>
                </div>
            </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                Oops! As informações para este item não foram encontradas ou o item não existe.
            </div>
            <div class="text-center mt-3">
                <a href="PaginaCards.php" class="btn btn-primary"> <i class="bi bi-arrow-left-circle"></i> Ver Outros Itens</a>
                <a href="index.php" class="btn btn-secondary ms-2"> <i class="bi bi-house"></i> Voltar para Início</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>