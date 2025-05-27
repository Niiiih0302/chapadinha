<?php

include '../api/v1/config/Database.php';
include '../includes/header.php';
include '../includes/head.php';
include '../includes/footer.php';

$conn = getConnection();

$pesquisa = $_GET['BarraPesquisa'] ?? '';    //O campo de texto tem o atributo name="q". Isso significa que, quando você envia o formulário, o valor digitado aparece na URL assim:

$query = "
SELECT 
    a.id,
    a.nome_cientifico,
    a.familia,
    a.genero,
    a.curiosidade,
    a.imagem,
    np.nome AS nome_popular,
    b.nome AS bioma,
    m.CAP,
    m.DAP,
    m.armotizacao,
    t.exotica_nativa,
    t.medicinal,
    t.toxica
FROM arvore a
LEFT JOIN nome_popular np ON np.fk_arvore = a.id
LEFT JOIN arvore_bioma ab ON ab.fk_arvore = a.id
LEFT JOIN bioma b ON b.id = ab.fk_bioma
LEFT JOIN medidas m ON m.fk_arvore = a.id
LEFT JOIN tipo_arvore t ON t.fk_arvore = a.id
WHERE 
    a.nome_cientifico LIKE ? OR 
    np.nome LIKE ? OR 
    a.familia LIKE ? OR 
    a.genero LIKE ?
";

$stmt = $conn->prepare($query); //Prepara uma query SQL com placeholders (?) para ser executada depois.
$like = "%$pesquisa%"; //Cria a string de busca com %, que o LIKE do SQL entende como "qualquer coisa antes ou depois".
//"ssss" significa que os 4 parâmetros são do tipo string:
/*s = string

i = integer

d = double

b = blob

Cada $like será associado a um ? na ordem em que aparecem.
*/
$stmt->bind_param("ssss", $like, $like, $like, $like); //Esse código é responsável por fazer uma consulta ao banco de dados com segurança, usando prepared statements (declarações preparadas) para evitar SQL Injection
$stmt->execute(); //Executa a consulta no banco de dados com os valores substituídos nos ?.
$result = $stmt->get_result(); //Captura o resultado da consulta (como se fosse o mysqli_query tradicional).

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='col'>";
        echo "<div class='card h-100'>";
        
        // Imagem
        if ($row['imagem']) {
            echo "<img src='img/" . $row['imagem'] . "' class='card-img-top' alt='Imagem da árvore' style='height: 200px; object-fit: cover;'>";
        } else {
            echo "<img src='img/sem-imagem.png' class='card-img-top' alt='Sem imagem' style='height: 200px; object-fit: cover;'>";
        }

        // Conteúdo do Card
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>" . htmlspecialchars($row['nome_popular'] ?? 'Sem nome popular') . "</h5>";
        echo "<p class='card-text'><strong>Nome Científico:</strong> " . $row['nome_cientifico'] . "</p>";
        echo "<p class='card-text'><strong>Família:</strong> " . $row['familia'] . "<br>";
        echo "<strong>Gênero:</strong> " . $row['genero'] . "<br>";
        echo "<strong>Bioma:</strong> " . $row['bioma'] . "</p>";
        echo "<p class='card-text'><strong>Curiosidade:</strong> " . $row['curiosidade'] . "</p>";
        echo "<p class='card-text'><strong>CAP:</strong> " . $row['CAP'] . " | <strong>DAP:</strong> " . $row['DAP'] . "</p>";
        echo "<p class='card-text'><strong>Exótica/Nativa:</strong> " . ($row['exotica_nativa'] ? 'Sim' : 'Não') . "<br>";
        echo "<strong>Medicinal:</strong> " . ($row['medicinal'] ? 'Sim' : 'Não') . "<br>";
        echo "<strong>Tóxica:</strong> " . ($row['toxica'] ? 'Sim' : 'Não') . "</p>";
        echo "<a href='#' class='btn btn-outline-success'>Ver mais</a>";
        echo "</div>"; // card-body
        echo "</div>"; // card
        echo "</div>"; // col
    }
} else {
    echo "<p>Nenhum resultado encontrado para '<strong>" . htmlspecialchars($pesquisa) . "</strong>'.</p>";
}

$conn->close();
?>


