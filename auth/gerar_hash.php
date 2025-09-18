<?php
// Define a URL base para carregar os arquivos de forma correta
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$uri_parts = explode('/', $_SERVER['REQUEST_URI']);
$base_url_path = '/';
if (in_array('admin', $uri_parts)) {
    $base_url_path = strstr($_SERVER['REQUEST_URI'], 'admin', true);
} else {
    $base_url_path = dirname($_SERVER['REQUEST_URI']) . '/';
}
$BASE_URL = $protocol . '://' . $host . $base_url_path;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Hash de Senha - CEMEAC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
 <link rel="stylesheet" href="../assets/css/painel.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card p-4">
            <h2 class="card-title text-center">Gerador de Hash de Senha</h2>

            <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="mb-3">
                    <label for="senha" class="form-label">Digite a Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Gerar Hash</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['senha']) && !empty($_POST['senha'])) {
                $senha_original = $_POST['senha'];
                $novo_hash = password_hash($senha_original, PASSWORD_DEFAULT);
                ?>
                <div class="resultado mt-4">
                    <h5 class="mb-3">Hash Gerado:</h5>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <pre class="hash-text m-0"><?= htmlspecialchars($novo_hash) ?></pre>
                        <button class="btn btn-sm btn-clipboard ms-auto" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($novo_hash) ?>')">
                            Copiar
                        </button>
                    </div>
                    <p class="mt-2 text-muted small">
                        Copie este hash e use-o nas suas consultas SQL `INSERT` ou `UPDATE` para novos usuários.
                    </p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('.btn-clipboard').addEventListener('click', function(event) {
            event.preventDefault();
            this.textContent = 'Copiado!';
            setTimeout(() => this.textContent = 'Copiar', 2000);
        });
    </script>
</body>
</html>