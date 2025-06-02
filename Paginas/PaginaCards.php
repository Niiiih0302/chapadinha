<?php 
include '../includes/head.php';
include '../includes/conexao.php'; 

$base_path = dirname($_SERVER['SCRIPT_NAME']);
$url_img = '/chapadinha/img/';
?>

<link rel="stylesheet" href="../Estilos/PagCardsEstilo.css">

<body>
<?php include '../includes/header.php'; ?>

<div class="cards-section">
    <div class="card-container">
        <?php
        // Consulta ao banco de dados
        $sql = "SELECT a.imagem, np.nome
                FROM arvore a
                LEFT JOIN nome_popular np ON a.id = np.fk_arvore
                GROUP BY a.id";

        $resultado = mysqli_query($conn, $sql);

        if (mysqli_num_rows($resultado) > 0) {
            $count = 0;
            while ($arvore = mysqli_fetch_assoc($resultado)) {
                // Quebra de linha a cada 3 cards
                if ($count > 0 && $count % 3 == 0) {
                    echo '</div><div class="card-container">';
                }
        ?>
                <div class="card">
                    <div class="content">
                        <div class="front">
                            <img src="<?php echo $url_img . $arvore['imagem']; ?>" alt="<?php echo $arvore['nome']; ?>">
                            <h3><?php echo $arvore['nome']; ?></h3>
                        </div>
                    </div>
                </div>
        <?php
                $count++;
            }
        } else {
            echo "<p>Nenhuma árvore encontrada.</p>";
        }
        ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
