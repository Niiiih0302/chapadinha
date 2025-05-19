<!-- principal.php -->

  <?php include '../includes/head.php'; ?>

  <title>Página Principal</title>

<body>

  <?php include '../includes/header.php'; ?>

  <main class="container">
    <div class="top-container">
        <div class="search-container">
          <form action="" method="get">
            <input type="text" id="search-bar" name="search-bar" placeholder="Ex: Árvore">
            <input type="submit" id="search-bt" value="Pesquisar">
          </form>
          <h1 id="welcome-msg">Bem Vindo!</h1>
        </div>
    </div>
    <!-- <div></div> especificar conteúdo -->
    <div class="card-container">
      <div class="card">
        <h2>Árvore</h2>
        <p>Descrição da árvore.</p>
        <a href="">Ver mais</a>
      </div>
      <div class="card">
        <h2>Lago</h2>
        <p>Descrição do lago.</p>
        <a href="">Ver mais</a>
      </div>
      <div class="card">
        <h2>Cupim</h2>
        <p>Descrição do cupim.</p>
        <a href="">Ver mais</a>
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>

</body>
</html>
