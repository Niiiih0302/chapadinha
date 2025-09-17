<?php 
include '../includes/head.php';
include '../includes/conexao.php'; 

$base_path = dirname($_SERVER['SCRIPT_NAME']);
$url_img = '/chapadinha/img/';

$tipo_filtro = $_GET['tipo'] ?? 'todos';
$titulo_catalogo = "Catálogo";
$subtitulo_catalogo = "Explore a rica biodiversidade da Lagoa da Chapadinha.";

$cupim_data = null;
$lagoa_data = null;

if ($tipo_filtro == 'todos' || $tipo_filtro == 'cupim') {
    $result_cupim = $conn->query("SELECT nome_popular, nome_cientifico, imagem FROM cupinzeiro WHERE id = 1");
    if($result_cupim) $cupim_data = $result_cupim->fetch_assoc();
}
if ($tipo_filtro == 'todos' || $tipo_filtro == 'lagoa') {
    $result_lagoa = $conn->query("SELECT nome_popular, imagem FROM lagoa WHERE id = 1");
    if($result_lagoa) $lagoa_data = $result_lagoa->fetch_assoc();
}


switch ($tipo_filtro) {
    case 'arvore':
        $titulo_catalogo = "Árvores Catalogadas";
        $subtitulo_catalogo = "Conheça as diversas espécies arbóreas que habitam a região.";
        break;
    case 'cupim':
        $titulo_catalogo = "Cupinzeiros";
        $subtitulo_catalogo = "Descubra a fascinante vida dos cupinzeiros e sua importância ecológica.";
        break;
    case 'lagoa':
        $titulo_catalogo = "Lagoa";
        $subtitulo_catalogo = "Aprenda sobre os ecossistemas aquáticos e seus habitantes.";
        break;
    default:
        $titulo_catalogo = "Catálogo Completo";
        $subtitulo_catalogo = "Explore toda a variedade de elementos naturais encontrados na Lagoa da Chapadinha.";
        break;
}

?>

<link rel="stylesheet" href="../Estilos/PagCardsEstilo.css">
<title><?php echo htmlspecialchars($titulo_catalogo); ?> - Lagoa da Chapadinha</title>
<style>

    .banner-catalogo {
        background-image: url('../img/banner-catalogos.jpg'); 
        background-size: cover;
        background-position: center;
        min-height: 30vh; 
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
        margin-top: -5.5rem; 
        padding-top: 5.5rem; 
        margin-bottom: 2rem; 
    }

    .banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); 
        z-index: 1;
    }

    .banner-content {
        position: relative;
        z-index: 2;
        padding: 20px;
        max-width: 900px;
    }

    .banner-content h1 {
        font-family: 'Marcellus', serif;
        font-size: 3rem;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
    }

    .banner-content p {
        font-family: 'Quicksand', sans-serif;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
    }

    .filter-buttons {
        margin-bottom: 2rem; 
    }
    .filter-buttons .btn {
        margin: 0.25rem;
        transition: all 0.3s ease; 
        font-weight: 600;
    }
    .filter-buttons .btn-success {
        background-color: #89b6a0; 
        border-color: #89b6a0;
        color: white;
    }
    .filter-buttons .btn-success:hover {
        background-color: #6f9683;
        border-color: #6f9683;
    }
    .filter-buttons .btn-outline-success {
        border-color: #89b6a0;
        color: #4b6043;
    }
    .filter-buttons .btn-outline-success:hover {
        background-color: #89b6a0;
        color: white;
    }
    .filter-buttons .btn-warning.text-dark {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529 !important;
    }
    .filter-buttons .btn-warning.text-dark:hover {
        background-color: #e0a800;
        border-color: #e0a800;
    }
    .filter-buttons .btn-outline-warning.text-dark {
        border-color: #ffc107;
        color: #212529;
    }
     .filter-buttons .btn-outline-warning.text-dark:hover {
        background-color: #ffc107;
        color: #212529;
    }
    .filter-buttons .btn-info.text-dark {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }
    .filter-buttons .btn-info.text-dark:hover {
        background-color: #138496;
        border-color: #138496;
    }
    .filter-buttons .btn-outline-info.text-dark {
        border-color: #17a2b8;
        color: #17a2b8;
    }
    .filter-buttons .btn-outline-info.text-dark:hover {
        background-color: #17a2b8;
        color: white;
    }

    .search-bar-container {
        background-color: #e9ecef;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 2.5rem; 
    }
    .search-bar-container .form-control {
        border-color: #89b6a0;
        box-shadow: none; 
    }
    .search-bar-container .form-control:focus {
        border-color: #6f9683; 
        box-shadow: 0 0 0 0.25rem rgba(137, 182, 160, 0.25); 
    }
    .search-bar-container .btn-primary {
        background-color: #89b6a0;
        border-color: #89b6a0;
    }
    .search-bar-container .btn-primary:hover {
        background-color: #7aa891;
        border-color: #7aa891;
    }

    .catalogo-title {
        display: none; 
    }

    @media (max-width: 768px) {
        .banner-content h1 {
            font-size: 2.2rem;
        }
        .banner-content p {
            font-size: 1rem;
        }
        .banner-catalogo {
            min-height: 25vh;
        }
    }
</style>

<body>
<?php include '../includes/header.php'; ?>

<div class="banner-catalogo">
    <div class="banner-overlay"></div>
    <div class="banner-content">
        <h1><?php echo htmlspecialchars($titulo_catalogo); ?></h1>
        <p><?php echo htmlspecialchars($subtitulo_catalogo); ?></p>
    </div>
</div>

<main class="container my-5">

    <div class="search-bar-container shadow-sm">
        <form class="d-flex justify-content-center" action="pesquisa.php" method="get">
            <input class="form-control form-control-lg w-75 me-2" type="text" name="BarraPesquisa" placeholder="Buscar por nome científico, popular, família ou gênero de árvore...">
            <button class="btn btn-primary btn-lg" type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                </svg>
                Pesquisar
            </button>
        </form>
    </div>

    <div class="container mb-4 text-center filter-buttons">
        <a href="PaginaCards.php?tipo=todos" class="btn <?php echo ($tipo_filtro == 'todos' ? 'btn-success' : 'btn-outline-success'); ?>">Todos</a>
        <a href="PaginaCards.php?tipo=arvore" class="btn <?php echo ($tipo_filtro == 'arvore' ? 'btn-success' : 'btn-outline-success'); ?>">Árvores</a>
        <a href="PaginaCards.php?tipo=lagoa" class="btn <?php echo ($tipo_filtro == 'lagoa' ? 'btn-info text-dark' : 'btn-outline-info text-dark'); ?>">Lagoa</a>
        <a href="PaginaCards.php?tipo=cupim" class="btn <?php echo ($tipo_filtro == 'cupim' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'); ?>">Cupinzeiros</a>
        
    </div>
    
    <div class="cards-section">
        <div class="card-container">
            <?php
            $itens_exibidos = 0;

            if ($tipo_filtro == 'todos' || $tipo_filtro == 'arvore') {

                $sql = "SELECT 
                            a.id, 
                            (SELECT ai.caminho_imagem FROM arvore_imagens ai WHERE ai.fk_arvore = a.id ORDER BY ai.id LIMIT 1) as imagem,
                            (SELECT np.nome FROM nome_popular np WHERE np.fk_arvore = a.id ORDER BY np.id LIMIT 1) AS nome_popular,
                            a.nome_cientifico
                        FROM arvore a
                        GROUP BY a.id
                        ORDER BY nome_popular, a.nome_cientifico";
                $resultado = mysqli_query($conn, $sql);

                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    while ($arvore = mysqli_fetch_assoc($resultado)) {
                        $itens_exibidos++;
            ?>
                        <div class="card">
                            <div class="content">
                                <div class="front">
                                    <img src="<?php echo $url_img . (!empty($arvore['imagem']) ? htmlspecialchars($arvore['imagem']) : 'placeholder-arvore.png'); ?>" alt="<?php echo htmlspecialchars($arvore['nome_popular'] ?? $arvore['nome_cientifico']); ?>">
                                    <h3><?php echo htmlspecialchars($arvore['nome_popular'] ?? $arvore['nome_cientifico']); ?></h3>
                                    <?php if(!empty($arvore['nome_popular']) && !empty($arvore['nome_cientifico']) && $arvore['nome_popular'] != $arvore['nome_cientifico']): ?>
                                        <p style="font-size:0.8em; margin-top: -5px; color: #555;"><em><?php echo htmlspecialchars($arvore['nome_cientifico']); ?></em></p>
                                    <?php endif; ?>
                                    <a href="PaginaDetalhes.php?type=arvore&id=<?php echo htmlspecialchars($arvore['id']); ?>" class="btn btn-card-details">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
            <?php
                    }
                }
            }

            if (($tipo_filtro == 'todos' || $tipo_filtro == 'cupim') && $cupim_data) {
                $itens_exibidos++;
            ?>
                <div class="card">
                    <div class="content">
                        <div class="front">
                            <img src="<?php echo $url_img . htmlspecialchars($cupim_data['imagem']); ?>" alt="<?php echo htmlspecialchars($cupim_data['nome_popular']); ?>">
                            <h3><?php echo htmlspecialchars($cupim_data['nome_popular']); ?></h3>
                            <p style="font-size:0.8em; margin-top: -5px; color: #555;"><em><?php echo htmlspecialchars($cupim_data['nome_cientifico']); ?></em></p>
                            <a href="PaginaDetalhes.php?type=cupim&id=1" class="btn btn-card-details">Ver Detalhes</a>
                        </div>
                    </div>
                </div>
            <?php
            }

            if (($tipo_filtro == 'todos' || $tipo_filtro == 'lagoa') && $lagoa_data) {
                $itens_exibidos++;
            ?>
                <div class="card">
                    <div class="content">
                        <div class="front">
                             <img src="<?php echo $url_img . htmlspecialchars($lagoa_data['imagem']); ?>" alt="<?php echo htmlspecialchars($lagoa_data['nome_popular']); ?>">
                            <h3><?php echo htmlspecialchars($lagoa_data['nome_popular']); ?></h3>
                            <p style="font-size:0.8em; margin-top: -5px; color: #555;"><em>Ecossistema Aquático</em></p>
                            <a href="PaginaDetalhes.php?type=lagoa&id=1" class="btn btn-card-details">Ver Detalhes</a>
                        </div>
                    </div>
                </div>
            <?php
            }
            
            if ($itens_exibidos == 0) {
                 echo "<p class='text-center w-100'>Nenhum item encontrado para este filtro.</p>";
            }
            ?>
        </div>
    </div>
</main>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>
</body>
</html>