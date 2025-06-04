<?php

?>
<link rel="stylesheet" href="../includes/header.css">
<header class="site-header">
  <div class="container">
    <div class="header-content">
      <div class="logo-container">
        <a href="/chapadinha/Paginas/SobreNos.php#sobre-fatec"><img src="../img/logoFatec.png" alt="Logo Fatec" class="logo-image"></a>
        <div class="logo-text"><a href="index.php">Lagoa da Chapadinha</a></div>
      </div>
      
      <nav class="nav-menu">
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Início</a>
        <a href="PaginaCards.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'PaginaCards.php' ? 'active' : ''; ?>">Catálogo</a>
        <a href="SobreNos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'SobreNos.php' ? 'active' : ''; ?>">Sobre Nós</a>
      </nav>
    </div>
  </div>
</header>