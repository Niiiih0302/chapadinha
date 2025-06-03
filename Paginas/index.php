<?php include '../includes/head.php'; ?>

<style>

  .chewy-font {
    font-family: 'Poppins', sans-serif;
    font-size: 3rem;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.7);
  }
  
  /* CSS existente para os cards e links temporários da index */
  .row.row-cols-1.row-cols-md-3.g-4 { 
    display: flex !important;
    flex-wrap: wrap !important;
    justify-content: space-between !important;
    margin: 0 -0.75rem !important; 
  }
  .row.row-cols-1.row-cols-md-3.g-4 > .col { 
    flex: 0 0 calc(33.333% - 1.5rem) !important; 
    padding: 0 0.75rem !important; 
    margin-bottom: 1.5rem !important; 
    display: flex;
  }
   .row.row-cols-1.row-cols-md-3.g-4 > .col > .card {
    width: 100%;
  }

  @media (max-width: 992px) {
    .row.row-cols-1.row-cols-md-3.g-4 > .col {
      flex: 0 0 calc(50% - 1.5rem) !important;
    }
  }

  @media (max-width: 768px) { 
    .row.row-cols-1.row-cols-md-3.g-4 > .col { 
      flex: 0 0 100% !important;
    }
    .chewy-font {
        font-size: 2.2rem;
    }
  }

  /* Novo CSS para a seção de destaque (Hero) */
  /* Remove o 'top: 0' e garante que a margem superior seja 0 */
  .hero-section {
    background-image: url('../img/principal.jpg');
    background-size: cover;
    background-position: center;
    min-height: 50vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
    /* Adicionado para garantir que o hero section comece logo após o header fixo */
    margin-top: -5.5rem; /* Isso "puxa" a seção para cima, sobrepondo o padding do body */
    padding-top: 5.5rem; /* Isso garante que o conteúdo dentro da seção não fique escondido */
  }

  .hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1;
  }

  .hero-content {
    position: relative;
    z-index: 2;
    padding: 20px;
    max-width: 800px;
  }

  .hero-content h1 {
    font-family: 'Marcellus', serif;
    font-size: 3.5rem;
    margin-bottom: 1rem;
    text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.9);
  }

  .hero-content p {
    font-family: 'Quicksand', sans-serif;
    font-size: 1.2rem;
    margin-bottom: 2rem;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.8);
  }

  .hero-content .btn-hero {
      background-color: #89b6a0;
      border-color: #89b6a0;
      font-size: 1.1rem;
      padding: 0.8rem 2rem;
      border-radius: 50px;
      transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.3s ease;
  }

  .hero-content .btn-hero:hover {
      background-color: #6f9683;
      border-color: #6f9683;
      transform: translateY(-3px);
  }

  /* Seção de informações gerais */
  .info-section {
      padding: 3rem 0;
      text-align: center;
      background-color: #f8f9fa;
      border-bottom: 1px solid #e9ecef;
      margin-bottom: 3rem;
  }

  .info-section h2 {
      font-family: 'Marcellus', serif;
      color: #4b6043;
      font-size: 2.5rem;
      margin-bottom: 1.5rem;
  }

  .info-section p {
      font-size: 1.1rem;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      color: #555;
      line-height: 1.6;
  }

  /* Ajuste para a publicidade */
  .ad-banner {
    background-color: #343a40;
    color: #fff;
    text-align: center;
    padding: 0.5rem 0;
    margin: 0;
    font-size: 0.9rem;
  }

</style>

<title>Página Principal - Lagoa da Chapadinha</title>

<body>

  <?php include '../includes/header.php'; ?>

  <div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1 id="welcome-msg">Bem-vindo à Lagoa da Chapadinha!</h1>
      <p>Explore a rica biodiversidade e a beleza natural deste incrível ecossistema. Descubra as árvores, a fauna e as formações geológicas que fazem deste lugar um tesouro ambiental.</p>
      <a href="PaginaCards.php" class="btn btn-primary btn-hero">Explorar Catálogo</a>
    </div>
  </div>

  <div class="ad-banner">
    <p class="m-0">Publicidade</p>
  </div>

  <section class="info-section">
      <div class="container">
          <h2>Um Projeto de Conscientização e Descoberta</h2>
          <p>Nosso objetivo é aproximar você da natureza da Lagoa da Chapadinha, oferecendo informações detalhadas sobre cada elemento natural. Através de um catálogo interativo, QR Codes e conteúdos educativos, esperamos inspirar a conservação e o amor pelo meio ambiente.</p>
          <a href="SobreNos.php" class="btn btn-outline-success mt-3">Saiba Mais Sobre Nós</a>
      </div>
  </section>

  <main class="container my-5">

    <h2 class="text-center mb-4" style="font-family: 'Marcellus', serif; color: #4b6043;">Destaques do Catálogo</h2>

    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="../img/arvore-card.png" class="card-img-top" alt="Árvores da Chapadinha" style="height: 200px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title" style="font-family: 'Marcellus', serif; color: #6f9683;">Espécies Arbóreas</h5>
            <p class="card-text">Descubra a variedade de árvores, nativas e exóticas, que compõem a flora da lagoa.</p>
            <a href="../Paginas/PaginaCards.php?tipo=arvore" class="btn btn-outline-primary">Ver Árvores</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="../img/lago-card.png" class="card-img-top" alt="O Lago da Chapadinha" style="height: 200px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title" style="font-family: 'Marcellus', serif; color: #6f9683;">O Ecossistema Aquático</h5>
            <p class="card-text">Conheça mais sobre a Lagoa, sua fauna, flora e importância ecológica.</p>
            <a href="../Paginas/PaginaCards.php?type=lago" class="btn btn-outline-primary">Ver Lagoa</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="../img/cupim-card.png" class="card-img-top" alt="Cupinzeiros e seu Impacto" style="height: 200px; object-fit: cover;">  
          <div class="card-body">
            <h5 class="card-title" style="font-family: 'Marcellus', serif; color: #6f9683;">Os Cupinzeiros</h5>
            <p class="card-text">Entenda a importância dos cupinzeiros e sua contribuição para o solo e o ambiente.</p>
            <a href="../Paginas/PaginaCards.php?type=cupim" class="btn btn-outline-primary">Ver Cupinzeiros</a>
          </div>
        </div>
      </div>
    </div>
    </main>

  <?php include '../includes/footer.php'; ?>

</body>
</html>