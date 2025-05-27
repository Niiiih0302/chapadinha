<?php
// Simula a busca de dados com base em um ID e tipo passados via GET.
// Em um cenário real, você obteria esses dados do banco de dados.
$itemId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '1'; // ID padrão
$itemType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'arvore'; // Tipo padrão

// Dados de exemplo - substitua pela sua lógica de banco de dados
$itemData = [];
$pageTitle = 'Detalhes do Item';

// Simulação de busca de dados
// Você usaria o $itemId e $itemType para buscar no banco
if ($itemType === 'arvore') {
    // Baseado nos campos da tabela 'arvore' e descrição do usuário
    $itemData = [
        'id' => $itemId,
        'nome_cientifico' => 'Arborea Exemplum ' . $itemId,
        'nome_popular' => 'Árvore Comum Exemplo',
        'familia' => 'Familiares Arboris',
        'genero' => 'Genus Lignum',
        'curiosidade' => 'Esta árvore é conhecida por sua longevidade e pela beleza de suas flores na primavera. Suas raízes ajudam a estabilizar o solo e suas folhas fornecem sombra refrescante. É uma espécie fundamental para o ecossistema local, servindo de alimento e abrigo para diversas aves e insetos. A madeira já foi muito utilizada na construção civil, mas hoje a espécie é protegida.',
        'imagem' => '../img/arvore-card.png', // Imagem de exemplo
        'medidas' => 'Altura média: 12-18m, Diâmetro da copa: 8-12m',
        'bioma' => 'Cerrado / Mata Atlântica', // Exemplo de bioma
        'tipo_arvore' => 'Nativa de grande porte' // Exemplo de tipo
    ];
    $pageTitle = $itemData['nome_popular'];
} else if ($itemType === 'cupim') {
    $itemData = [
        'id' => $itemId,
        'nome_cientifico' => 'Termitidae Structuris ' . $itemId,
        'nome_popular' => 'Cupinzeiro da Chapadinha',
        'familia' => 'Termitidae',
        'genero' => 'Cornitermes',
        'curiosidade' => 'Os cupinzeiros são ecossistemas em miniatura, abrigando não apenas cupins, mas também outros insetos e até pequenos vertebrados. A estrutura interna é complexa, com túneis e câmaras para ventilação, cultivo de fungos e armazenamento de alimentos. Eles são essenciais para a ciclagem de nutrientes no solo.',
        'imagem' => '../img/cupim-card.png', // Imagem de exemplo
        'habitat' => 'Campos abertos e bordas de mata',
        'dieta' => 'Material vegetal em decomposição, celulose',
        'importancia_ecologica' => 'Decomposição de matéria orgânica, aeração do solo, fonte de alimento.'
    ];
    $pageTitle = $itemData['nome_popular'];
} else if ($itemType === 'lago') {
     $itemData = [
        'id' => $itemId,
        'nome_popular' => 'Lagoa da Chapadinha',
        'descricao_geral' => 'A Lagoa da Chapadinha é um ponto central da vida selvagem local, oferecendo recursos hídricos vitais e um habitat diversificado. Sua conservação é crucial para manter o equilíbrio ecológico da região. Atividades de educação ambiental são frequentemente realizadas em suas margens para conscientizar a população sobre sua importância.',
        'imagem' => '../img/lago-card.png', // Imagem de exemplo
        'localizacao' => 'Parque Municipal da Chapadinha',
        'tipo_agua' => 'Doce, com pH neutro',
        'fauna_destaque' => 'Peixes como lambaris e traíras, aves aquáticas como garças e patos-selvagens, além de capivaras.',
        'flora_destaque' => 'Presença de aguapés, taboas e vegetação ciliar nativa que protege as margens contra erosão.'
    ];
    $pageTitle = $itemData['nome_popular'];
}
// Adicione mais 'else if' para outros tipos de itens (ex: rochas, animais específicos)

include '../includes/head.php'; // Inclui o head padrão
?>
<link rel="stylesheet" href="../Estilos/PaginaDetalhesEstilo.css">
<title><?php echo htmlspecialchars($pageTitle); ?> - Lagoa da Chapadinha</title>

<body>
    <?php include '../includes/header.php'; // Inclui o cabeçalho padrão ?>

    <main class="container my-5">
        <?php if (!empty($itemData)): ?>
            <div class="detalhes-container shadow-lg">
                <div class="detalhes-imagem-wrapper">
                    <img src="<?php echo htmlspecialchars($itemData['imagem']); ?>" alt="Imagem de <?php echo htmlspecialchars($pageTitle); ?>" class="img-fluid rounded">
                </div>
                <div class="detalhes-info">
                    <h1 class="nome-item mb-3"><?php echo htmlspecialchars($pageTitle); ?></h1>

                    <?php if ($itemType === 'arvore'): ?>
                        <p><strong>Nome Científico:</strong> <?php echo htmlspecialchars($itemData['nome_cientifico']); ?></p>
                        <?php if(isset($itemData['nome_popular']) && $itemData['nome_popular'] !== $pageTitle): ?>
                            <p><strong>Nome Popular:</strong> <?php echo htmlspecialchars($itemData['nome_popular']); ?></p>
                        <?php endif; ?>
                        <p><strong>Família:</strong> <?php echo htmlspecialchars($itemData['familia']); ?></p>
                        <p><strong>Gênero:</strong> <?php echo htmlspecialchars($itemData['genero']); ?></p>
                        <p><strong>Medidas Comuns:</strong> <?php echo htmlspecialchars($itemData['medidas']); ?></p>
                        <p><strong>Bioma Principal:</strong> <?php echo htmlspecialchars($itemData['bioma']); ?></p>
                        <p><strong>Tipo de Árvore:</strong> <?php echo htmlspecialchars($itemData['tipo_arvore']); ?></p>
                        <h3 class="mt-4">Curiosidades:</h3>
                        <p class="text-indent"><?php echo nl2br(htmlspecialchars($itemData['curiosidade'])); ?></p>
                    <?php elseif ($itemType === 'cupim'): ?>
                        <p><strong>Nome Científico:</strong> <?php echo htmlspecialchars($itemData['nome_cientifico']); ?></p>
                        <?php if(isset($itemData['nome_popular']) && $itemData['nome_popular'] !== $pageTitle): ?>
                            <p><strong>Nome Popular:</strong> <?php echo htmlspecialchars($itemData['nome_popular']); ?></p>
                        <?php endif; ?>
                        <p><strong>Família:</strong> <?php echo htmlspecialchars($itemData['familia']); ?></p>
                        <p><strong>Gênero:</strong> <?php echo htmlspecialchars($itemData['genero']); ?></p>
                        <p><strong>Habitat Típico:</strong> <?php echo htmlspecialchars($itemData['habitat']); ?></p>
                        <p><strong>Dieta Principal:</strong> <?php echo htmlspecialchars($itemData['dieta']); ?></p>
                        <p><strong>Importância Ecológica:</strong> <?php echo htmlspecialchars($itemData['importancia_ecologica']); ?></p>
                        <h3 class="mt-4">Mais Informações:</h3>
                        <p class="text-indent"><?php echo nl2br(htmlspecialchars($itemData['curiosidade'])); ?></p>
                    <?php elseif ($itemType === 'lago'): ?>
                         <p><strong>Localização:</strong> <?php echo htmlspecialchars($itemData['localizacao']); ?></p>
                         <p><strong>Tipo de Água:</strong> <?php echo htmlspecialchars($itemData['tipo_agua']); ?></p>
                         <p><strong>Fauna em Destaque:</strong> <?php echo htmlspecialchars($itemData['fauna_destaque']); ?></p>
                         <p><strong>Flora em Destaque:</strong> <?php echo htmlspecialchars($itemData['flora_destaque']); ?></p>
                         <h3 class="mt-4">Descrição Geral:</h3>
                         <p class="text-indent"><?php echo nl2br(htmlspecialchars($itemData['descricao_geral'])); ?></p>
                    <?php endif; ?>

                    <div class="mt-4 pt-3 border-top">
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

    <?php include '../includes/footer.php'; // Inclui o rodapé padrão ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>