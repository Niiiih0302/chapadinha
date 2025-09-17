<?php 
include '../includes/head.php'; 
include '../includes/conexao.php'; 

$lagoa_card_data = null;
$cupim_card_data = null;

$result_lagoa = $conn->query("SELECT nome_popular, imagem FROM lagoa WHERE id = 1");
if ($result_lagoa) {
    $lagoa_card_data = $result_lagoa->fetch_assoc();
}

$result_cupim = $conn->query("SELECT nome_popular, imagem FROM cupinzeiro WHERE id = 1");
if ($result_cupim) {
    $cupim_card_data = $result_cupim->fetch_assoc();
}

?>

<style>

  .chewy-font {
    font-family: 'Poppins', sans-serif;
    font-size: 3rem;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.7);
  }
  
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
    margin-top: -5.5rem; 
    padding-top: 5.5rem; 
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
          <div class="share-container mt-4">
    <button id="main-share-btn" class="btn btn-dark">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-share-fill" viewBox="0 0 16 16" style="margin-top: -3px;">
            <path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z"/>
        </svg>
        Compartilhar esta página
    </button>

    <div id="share-options" class="card shadow-sm">
        <button id="copy-link-btn" class="btn btn-light text-start">Copiar Link</button>
        <a href="#" id="download-qr-btn" class="btn btn-light text-start" download="qrcode-lagoa-chapadinha.png">Baixar QR Code</a>
    </div>
</div>
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
          <img src="../img/<?php echo htmlspecialchars($lagoa_card_data['imagem'] ?? 'lago-card.png'); ?>" class="card-img-top" alt="O Lago da Chapadinha" style="height: 200px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title" style="font-family: 'Marcellus', serif; color: #6f9683;">O Ecossistema Aquático</h5>
            <p class="card-text">Conheça mais sobre a Lagoa, sua fauna, flora e importância ecológica.</p>
            <a href="../Paginas/PaginaCards.php?tipo=lagoa" class="btn btn-outline-primary">Ver Lagoa</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="../img/<?php echo htmlspecialchars($cupim_card_data['imagem'] ?? 'cupim-card.png'); ?>" class="card-img-top" alt="Cupinzeiros e seu Impacto" style="height: 200px; object-fit: cover;">  
          <div class="card-body">
            <h5 class="card-title" style="font-family: 'Marcellus', serif; color: #6f9683;">Os Cupinzeiros</h5>
            <p class="card-text">Entenda a importância dos cupinzeiros e sua contribuição para o solo e o ambiente.</p>
            <a href="../Paginas/PaginaCards.php?tipo=cupim" class="btn btn-outline-primary">Ver Cupinzeiros</a>
          </div>
        </div>
      </div>
    </div>
    </main>

  <?php 
  $conn->close();
  include '../includes/footer.php'; 
  ?>
<div id="hidden-qrcode" style="display:none;"></div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const textoAbaixoDoQr = "Lagoa da Chapadinha"; 

        const mainShareBtn = document.getElementById('main-share-btn');
        const shareOptions = document.getElementById('share-options');
        const copyLinkBtn = document.getElementById('copy-link-btn');
        const downloadQrBtn = document.getElementById('download-qr-btn');
        const hiddenQrDiv = document.getElementById('hidden-qrcode');

        const currentPageUrl = window.location.href;
        let isQrCodeGenerated = false;

        mainShareBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            const isMenuVisible = shareOptions.style.display === 'block';
            shareOptions.style.display = isMenuVisible ? 'none' : 'block';

            if (!isMenuVisible && !isQrCodeGenerated) {

                hiddenQrDiv.innerHTML = '';

                new QRCode(hiddenQrDiv, { text: currentPageUrl, width: 256, height: 256 });

                setTimeout(() => {
                    const originalCanvas = hiddenQrDiv.querySelector('canvas');
                    if (originalCanvas) {
                        const finalImageWithText = addTextToCanvas(originalCanvas, textoAbaixoDoQr);
                        downloadQrBtn.href = finalImageWithText; 
                        isQrCodeGenerated = true;
                    }
                }, 200); 
            }
        });
        
        function addTextToCanvas(originalCanvas, text) {
            const qrSize = originalCanvas.width;
            const padding = 15; 
            const fontSize = 16;
            const spaceForText = fontSize + (padding * 2); 

          
            const newCanvas = document.createElement('canvas');
            const ctx = newCanvas.getContext('2d');
            
            newCanvas.width = qrSize;
            newCanvas.height = qrSize + spaceForText;

            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);
            
            ctx.drawImage(originalCanvas, 0, 0);

            ctx.fillStyle = '#000000'; 
            ctx.font = `bold ${fontSize}px Arial`;
            ctx.textAlign = 'center';
            ctx.fillText(text, qrSize / 2, qrSize + padding + fontSize);

            return newCanvas.toDataURL('image/png');
        }

        copyLinkBtn.addEventListener('click', () => {
            navigator.clipboard.writeText(currentPageUrl).then(() => {
                copyLinkBtn.textContent = 'Copiado!';
                setTimeout(() => {
                    copyLinkBtn.textContent = 'Copiar Link';
                    shareOptions.style.display = 'none';
                }, 1500);
            }).catch(err => {
                alert('Falha ao copiar o link.');
            });
        });
        
        window.addEventListener('click', () => {
            if (shareOptions.style.display === 'block') {
                shareOptions.style.display = 'none';
            }
        });
    });
</script>

<style>

.share-container {
    position: relative;
    display: inline-block;
}

#share-options {
    position: absolute;
    top: 100%; 
    left: 0;
    z-index: 100;
    min-width: 220px;
    margin-top: 8px; 
    display: none; 
    padding: 0.5rem;
    border: 1px solid #ddd;
}
#share-options .btn {
    width: 100%; 
}
#share-options .btn:first-child {
    margin-bottom: 5px; 
}
  </style>
</body>
</html>