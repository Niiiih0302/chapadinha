<?php
// Inicia a sessão
session_start();

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['usuario_logado'])) {
    header('Location: arvores.php');
    exit;
}

// Verifica se há mensagem de erro\
$erro = isset($_SESSION['login_erro']) ? $_SESSION['login_erro'] : '';
unset($_SESSION['login_erro']); // Limpa a mensagem de erro
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatec News - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2>Chapadinha</h2>
                <p>Área Administrativa</p>
            </div>
            
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <form action="processa_login.php" method="post">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Entrar</button>
                </div>
            </form>
            <div class="mt-3 text-center">
                <a href="../Paginas/index.php">Voltar para o site</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>