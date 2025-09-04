<?php
// pesquisa.php
include '../includes/head.php'; // Para tags <head> e Bootstrap CSS
include '../includes/conexao.php'; // Para conexão com o banco

$url_img = '/chapadinha/img/'; // Certifique-se que este caminho está correto a partir da raiz do servidor
$pesquisa = $_GET['BarraPesquisa'] ?? '';

?>
<link rel="stylesheet" href="../Estilos/PagCardsEstilo.css"> <title>Resultados da Pesquisa - Lagoa da Chapadinha</title>
<style>
    main {
        /* A propriedade height foi removida para permitir que o conteúdo defina a altura, o que funciona melhor com o sticky footer. */
    }

    .search-results-title {
        font-family: 'Marcellus', serif;
        color: #4b6043;
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2.2rem;
    }
    .back-to-catalog-container {
        text-align: center;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    .suggestion-title {
        font-family: 'Quicksand', sans-serif;
        color: #555;
        text-align: center;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    /* Efeito de hover no card */
    .cards-section .front:hover {
    transform: translateY(-1px); 
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); 
    border-color: #89b6a0; 
    }
    
    /* Media query para ajustar o título em telas menores */
    @media (max-width: 768px) {
        .search-results-title {
            font-size: 1.8rem; /* Reduz o tamanho da fonte do título principal */
        }
        .suggestion-title {
            font-size: 1.2rem; /* Reduz o tamanho da fonte do título de sugestões */
        }
    }

</style>

<body>
<?php 
include '../includes/header.php'; // Inclui o cabeçalho padrão
?>

<main class="container my-5">
    <h2 class="search-results-title">
        Resultados da Pesquisa para: "<?php echo htmlspecialchars($pesquisa); ?>"
    </h2>

    <div class="cards-section">
        <div class="card-container">
            <?php
            $resultados_encontrados_direto = false;
            $sugestoes_encontradas = false;

            if (!empty($pesquisa)) {
                
                $query_like = "
                    SELECT 
                        a.id,
                        a.nome_cientifico,
                        a.familia,
                        a.genero,
                        a.curiosidade,
                        ai.caminho_imagem,
                        GROUP_CONCAT(DISTINCT np.nome SEPARATOR ', ') AS nomes_populares
                    FROM arvore a
                    LEFT JOIN nome_popular np ON np.fk_arvore = a.id
                    LEFT JOIN arvore_imagens ai ON ai.fk_arvore = a.id
                    WHERE 
                        a.nome_cientifico LIKE ? OR 
                        np.nome LIKE ? OR 
                        a.familia LIKE ? OR 
                        a.genero LIKE ?
                    GROUP BY a.id
                    ORDER BY a.nome_cientifico";

                $stmt_like = $conn->prepare($query_like);
                $like_term = "%$pesquisa%";
                $stmt_like->bind_param("ssss", $like_term, $like_term, $like_term, $like_term);
                $stmt_like->execute();
                $result_like = $stmt_like->get_result();

                if ($result_like->num_rows > 0) {
                    $resultados_encontrados_direto = true;
                    while ($row = $result_like->fetch_assoc()) {
                        $nome_exibicao = !empty($row['nomes_populares']) ? explode(',', $row['nomes_populares'])[0] : $row['nome_cientifico'];
            ?>
                        <div class="card">
                            <div class="content">
                                <div class="front">
                                    <a href="PaginaDetalhes.php?type=arvore&id=<?php echo htmlspecialchars($row['id']); ?>" style="text-decoration: none; color: inherit;">
                                        <img src="<?php echo $url_img . (!empty($row['caminho_imagem']) ? htmlspecialchars($row['caminho_imagem']) : 'sem-imagem.png'); ?>" alt="<?php echo htmlspecialchars($nome_exibicao); ?>">
                                        <h3><?php echo htmlspecialchars($nome_exibicao); ?></h3>
                                        <?php if ($nome_exibicao != $row['nome_cientifico'] && !empty($row['nome_cientifico'])): ?>
                                            <p style="font-size:0.8em; margin-top: -5px; color: #555;"><em><?php echo htmlspecialchars($row['nome_cientifico']); ?></em></p>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
            <?php
                    }
                }
                $stmt_like->close();

                // Se não houver resultados diretos, tentar busca fonética
                if (!$resultados_encontrados_direto) {
                    echo "<p class='text-center w-100'>Nenhum resultado direto encontrado para '<strong>" . htmlspecialchars($pesquisa) . "</strong>'.</p>";

                    
                    if (str_word_count($pesquisa) <= 3) { 
                        $query_soundex = "
                            SELECT 
                                a.id, a.nome_cientifico, ai.caminho_imagem,
                                GROUP_CONCAT(DISTINCT np.nome SEPARATOR ', ') AS nomes_populares
                            FROM arvore a
                            LEFT JOIN nome_popular np ON np.fk_arvore = a.id
                            WHERE SOUNDEX(a.nome_cientifico) = SOUNDEX(?) OR SOUNDEX(np.nome) = SOUNDEX(?)
                            GROUP BY a.id
                            LIMIT 3"; // Limitar o número de sugestões fonéticas

                        $stmt_soundex = $conn->prepare($query_soundex);
                        if ($stmt_soundex) {
                            $stmt_soundex->bind_param("ss", $pesquisa, $pesquisa);
                            $stmt_soundex->execute();
                            $result_soundex = $stmt_soundex->get_result();

                            if ($result_soundex->num_rows > 0) {
                                $sugestoes_encontradas = true;
                                echo "<h4 class='suggestion-title w-100'>Talvez você quis dizer:</h4>";
                                while ($row_soundex = $result_soundex->fetch_assoc()) {
                                    $nome_exibicao_soundex = !empty($row_soundex['nomes_populares']) ? explode(',', $row_soundex['nomes_populares'])[0] : $row_soundex['nome_cientifico'];
            ?>
                                    <div class="card">
                                        <div class="content">
                                            <div class="front">
                                                <a href="PaginaDetalhes.php?type=arvore&id=<?php echo htmlspecialchars($row_soundex['id']); ?>" style="text-decoration: none; color: inherit;">
                                                    <img src="<?php echo $url_img . (!empty($row_soundex['imagem']) ? htmlspecialchars($row_soundex['imagem']) : 'sem-imagem.png'); ?>" alt="<?php echo htmlspecialchars($nome_exibicao_soundex); ?>">
                                                    <h3><?php echo htmlspecialchars($nome_exibicao_soundex); ?></h3>
                                                    <?php if ($nome_exibicao_soundex != $row_soundex['nome_cientifico'] && !empty($row_soundex['nome_cientifico'])): ?>
                                                        <p style="font-size:0.8em; margin-top: -5px; color: #555;"><em><?php echo htmlspecialchars($row_soundex['nome_cientifico']); ?></em></p>
                                                    <?php endif; ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
            <?php
                                }
                            }
                            $stmt_soundex->close();
                        }
                    }
                }
                // Se, mesmo após a busca fonética (se aplicável), nada foi encontrado no geral
                if (!$resultados_encontrados_direto && !$sugestoes_encontradas) {
                  
                }


            } else { 
                echo "<p class='text-center w-100'>Por favor, digite um termo para pesquisar.</p>";
            }
            ?>
        </div> </div> <div class="back-to-catalog-container">
        <a href="PaginaCards.php" class="btn btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
            </svg>
            Voltar ao Catálogo
        </a>
    </div>

</main>

<?php 
if (isset($conn)) { 
    $conn->close();
}
include '../includes/footer.php';  
?>
</body>
</html>