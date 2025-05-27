<?php include '../includes/head.php'; ?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Chewy&display=swap');

  .chewy-font {
    font-family: 'Chewy', cursive;
    font-size: 3rem;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.7);
  }
  
  /* ... (seu CSS existente para os cards e links temporários) ... */
  .row.row-cols-1.row-cols-md-3.g-4 { /* */
    display: flex !important;
    flex-wrap: wrap !important;
    justify-content: space-between !important;
    margin: 0 -0.75rem !important; 
  }
  .row.row-cols-1.row-cols-md-3.g-4 > .col { /* */
    flex: 0 0 calc(33.333% - 1.5rem) !important; 
    padding: 0 0.75rem !important; 
    margin-bottom: 1.5rem !important; 
  }
  @media (max-width: 768px) { /* */
    .row.row-cols-1.row-cols-md-3.g-4 > .col { /* */
      flex: 0 0 100% !important;
    }
  }

</style>

<title>Página Principal - Lagoa da Chapadinha</title>

<body>

  <?php include '../includes/header.php'; ?>

  <div class="position-relative text-white text-center d-flex align-items-center justify-content-center px-0 pt-0"
       style="background-image: url('../img/chapadinha-bg.png'); background-size: cover; background-position: center; min-height: 40vh;">
    
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(0, 0, 0, 0.5);"></div>

    <div class="position-relative container">
      <h1 id="welcome-msg" class="mb-4 chewy-font">Bem-vindo à Chapadinha!</h1>
      <form class="d-flex justify-content-center" action="" method="get">
        <input class="form-control w-50 me-2" type="text" id="search-bar" name="search-bar" placeholder="Ex: Árvore">
        <button class="btn btn-primary" id="search-bt" type="submit">Pesquisar</button>
      </form>
    </div>
  </div>

  <div class="bg-dark text-white text-center" style="padding: 0; margin: 0;">
    <p class="m-0 py-2">Publicidade</p>
  </div>


  <main class="container my-5">

    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div class="col">
        <div class="card h-100">
          <img src="../img/arvore-card.png" class="card-img-top" alt="Descrição da Árvore" style="height: 200px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title">Árvore</h5>
            <p class="card-text">Descrição da árvore.</p>
            <a href="../Paginas/PaginaDetalhes.php?type=arvore&id=1" class="btn btn-outline-primary">Ver mais</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100">
          <img src="../img/lago-card.png" class="card-img-top" alt="Descrição do Lago" style="height: 200px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title">Lago</h5>
            <p class="card-text">Descrição do lago.</p>
            <a href="../Paginas/PaginaDetalhes.php?type=lago&id=1" class="btn btn-outline-primary">Ver mais</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100">
          <img src="../img/cupim-card.png" class="card-img-top" alt="Descrição do Cupinzeiro" style="height: 200px; object-fit: cover;">  
          <div class="card-body">
            <h5 class="card-title">Cupim</h5>
            <p class="card-text">Descrição do cupim.</p>
            <a href="../Paginas/PaginaDetalhes.php?type=cupim&id=1" class="btn btn-outline-primary">Ver mais</a>
          </div>
        </div>
      </div>
    </div>
    </main>

  <?php include '../includes/footer.php'; ?>

</body>
</html>