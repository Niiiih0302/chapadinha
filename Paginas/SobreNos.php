<?php include '../includes/head.php'; ?>
<link rel="stylesheet" href="../Estilos/SobreNosEstilo.css">
<title>Sobre Nós - Projeto Lagoa da Chapadinha</title>

<body>
    <?php include '../includes/header.php'; ?>

    <main class="container my-5">
        <header class="text-center mb-5 page-title-container">
            <h1 class="page-title">Sobre Nosso Projeto e Equipe</h1>
            <p class="lead">Conheça mais sobre a iniciativa da Lagoa da Chapadinha, a Fatec e os estudantes por trás deste trabalho.</p>
        </header>

        <section id="sobre-projeto" class="mb-5 content-section">
            <h2 class="section-title">O Projeto Lagoa da Chapadinha</h2>
            <p>Este projeto visa criar uma plataforma digital interativa para catalogar e apresentar informações sobre a rica biodiversidade e os elementos naturais encontrados na Lagoa da Chapadinha. Através de QR Codes espalhados pelo local e um website informativo, buscamos promover a educação ambiental, o turismo consciente e a valorização do patrimônio natural da nossa região.</p>
            <p>A ideia é que, ao escanear um QR Code em uma árvore ou formação geológica, por exemplo, o visitante seja direcionado para uma página com dados detalhados sobre aquele item específico. Além disso, o site oferece uma galeria para explorar todos os elementos catalogados.</p>
        </section>

        <section id="sobre-fatec" class="mb-5 content-section">
            <h2 class="section-title">Sobre a Fatec</h2>
            <div class="fatec-content">
                <img src="../img/logoFatec.png" alt="Logo Fatec Horizontal" class="fatec-logo img-fluid mb-3"> <p>A Faculdade de Tecnologia (Fatec) é uma renomada instituição de ensino superior pública e gratuita do estado de São Paulo, mantida pelo Centro Paula Souza. Reconhecida pela excelência na formação de tecnólogos, a Fatec oferece cursos sintonizados com as mais atuais demandas do mercado de trabalho, proporcionando uma educação de alta qualidade que impulsiona a carreira e o desenvolvimento profissional de seus estudantes.</p>
                <p>Este projeto é um exemplo do conhecimento aplicado e da dedicação dos alunos da Fatec Itapetininga (ou a unidade correspondente), desenvolvido como parte fundamental de suas atividades acadêmicas, demonstrando na prática as competências adquiridas ao longo do curso.</p>
                <p><strong>Quer transformar seu futuro com uma educação de ponta e foco no mercado?</strong></p>
                <p>Descubra os cursos oferecidos e prepare-se para uma carreira de sucesso. Visite o site oficial para mais informações sobre o processo seletivo e como se inscrever!</p>
                <a href="https://fatecitapetininga.edu.br/" class="btn btn-success btn-lg mt-3" target="_blank">Inscreva-se na Fatec!</a>
            </div>
        </section>

        <section id="nossa-equipe" class="mb-5 content-section">
            <h2 class="section-title">Nossa Equipe</h2>
            <p class="text-center mb-4">Conheça os 8 talentosos estudantes desenvolvedores deste projeto:</p>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php
                $integrantes = [
                    ["nome" => "Luiz Guilherme de Queiroz Soares", "funcao" => "Desenvolvedor(a) Frontend", "icone" => "..\img\github-logo.png", "github" => "LuizGQS", "foto" => "..\img\FotoDev\FotoDeLuizGuilhermeDeQueiroz.png", "link" => "https://github.com/LuizGQS"],
                    ["nome" => "Marcos Siqueira Santos", "funcao" => "Desenvolvedor(a) Frontend","icone" => "..\img\github-logo.png", "github" => "MarcosSiqueiraSantos" ,"foto" => "..\img\FotoDev\FotoDeMarcosSiqueiraSantos.png", "link" => "https://github.com/MarcosSiqueiraSantos"],
                    ["nome" => "Nathan Lucas de Paula Vieira", "funcao" => "Desenvolvedor(a) Frontend", "icone" => "..\img\github-logo.png" ,"github" => "NathanLucasVieira", "foto" => "..\img\FotoDev\FotoDeNathanLucasDePaulaVieira.jpg", "link" => "https://github.com/NathanLucasVieira"],
                    ["nome" => "Renan de Castro Machado", "funcao" => "Desenvolvedor(a) Frontend", "icone" => "..\img\github-logo.png", "github" => "zCastroz" ,"foto" => "..\img\FotoDev\FotoDeRenanDeCastro.png", "link" => "https://github.com/zCastroz"],
                    ["nome" => "Julianne Rodrigues Barbosa", "funcao" => "Desenvolvedor(a) Backend", "icone" => "..\img\github-logo.png", "github" => "Julianne-Barbosa","foto" => "..\img\FotoDev\FotoDeJulianneBarbosa.jpg", "link" => "https://github.com/Julianne-Barbosa"],
                    ["nome" => "Maria Clara Chiromito Trombeta", "funcao" => "Desenvolvedor(a) Backend","icone" => "..\img\github-logo.png", "github" => "mariachiromito" ,"foto" => "..\img\FotoDev\FotoDeMariaClara.png", "link" => "https://github.com/mariachiromito"],
                    ["nome" => "Naiane Rivia De Jesus Oliveira", "funcao" => "Desenvolvedor(a) Backend","icone" => "..\img\github-logo.png", "github" => "Naiane20Oliveira" ,"foto" => "..\img\FotoDev\FotoDeNaiane.png", "link" => "https://github.com/Naiane20Oliveira"],
                    ["nome" => "Nicole Rodrigues Dos Santos", "funcao" => "Desenvolvedor(a) Backend","icone" => "..\img\github-logo.png", "github" => "Niiiih0302" ,"foto" => "..\img\FotoDev\FotoDeNicoleSantos.jpg", "link" => "https://github.com/Niiiih0302"],
                ];

                foreach ($integrantes as $integrante) {
                ?>
                    <div class="col">
                        <div class="card h-100 text-center integrante-card">
                            <img src="<?php echo htmlspecialchars($integrante['foto']); ?>" class="card-img-top integrante-foto" alt="Foto de <?php echo htmlspecialchars($integrante['nome']); ?>">
                            <div class="card-body">
                                <h5 class="card-title integrante-nome"><?php echo htmlspecialchars($integrante['nome']); ?></h5>
                                <p class="card-text integrante-funcao"><?php echo htmlspecialchars($integrante['funcao']); ?></p>
                                
                                    <a id = "link-github" href = "<?php echo htmlspecialchars($integrante['link']); ?>">
                                    <img src="<?php echo htmlspecialchars($integrante['icone']); ?>" class="card-img-top integrante-icone" alt="Foto de <?php echo htmlspecialchars($integrante['github']); ?>">
                                    <p class="card-text integrante-github"><?php echo htmlspecialchars($integrante['github']); ?></p>
                                    </a>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </section>

    </main>

    <?php include '../includes/footer.php'; ?>
    </body>
</html>