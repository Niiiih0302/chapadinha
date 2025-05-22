<!-- header.php -->
<link rel="stylesheet" href="../includes/header.css">

<header class="site-header">
  <div class="container">
    <div class="header-content">
      <!-- Logo -->
      <div class="logo-container">
        <img src="../img/logoFatec.png" alt="Logo Fatec" class="logo-image">
        <div class="logo-text">Lagoa da Chapadinha</div>
      </div>
      
      <!-- Menu de navegação -->
      <nav class="nav-menu">
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Início</a>
      </nav>
    </div>
  </div>
</header>