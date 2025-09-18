<?php
require_once('../config/config.php');
require_once('../config/database.php');

// ----- INÍCIO DO BLOQUEIO ESTILIZADO -----
die('<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Desativado</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body { background-color: #121212; color: #e0e0e0; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; text-align: center; }
        .container-box { background-color: #1f1f1f; padding: 40px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5); }
        .title { color: #ff7700; font-weight: bold; }
        .message { margin-top: 20px; }
        .btn-return { margin-top: 30px; background-color: #ff7700; border-color: #ff7700; color: #fff; }
        .btn-return:hover { background-color: #e66c00; border-color: #e66c00; }
    </style>
</head>
<body>
    <div class="container-box">
        <h2 class="title">Acesso Temporariamente Desativado</h2>
        <p class="message">Esta funcionalidade está em manutenção. Por favor, tente novamente mais tarde.</p>
        <a href="login.php" class="btn btn-lg btn-return">Voltar para o Login</a>
    </div>
</body>
</html>');
// ----- FIM DO BLOQUEIO ESTILIZADO -----

$erro = '';
$sucesso = '';
$token_valido = false;

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
    
    try {
        // Verificar token no banco de dados
        $sql = "SELECT id FROM usuarios WHERE reset_token = ? AND reset_token_expiry > NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);
        
        if ($stmt->fetch()) { // Retorna a linha se o token for válido e não estiver expirado
            $token_valido = true;
        } else {
            $erro = "Token inválido ou expirado.";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao verificar o token: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if ($nova_senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    } else {
        try {
            // Verificar token novamente
            $sql = "SELECT id FROM usuarios WHERE reset_token = ? AND reset_token_expiry > NOW()";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$token]);
            $usuario_id = $stmt->fetchColumn(); // Pega apenas o ID
            
            if ($usuario_id) {
                // Hash da nova senha
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                
                // Atualizar senha e limpar token
                $update_sql = "UPDATE usuarios SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([$senha_hash, $usuario_id]);
                
                $sucesso = "Senha redefinida com sucesso! Faça login com a nova senha.";
                // Redirecionar para login após 3 segundos
                header("refresh:3;url=login.php");
            } else {
                $erro = "Token inválido ou expirado.";
            }
        } catch (PDOException $e) {
            $erro = "Erro ao redefinir a senha: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - CEMEAC</title>
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/painel.css">
    <style>
        body {
            background-color: #121212;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .reset-container {
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2 class="text-center mb-4" style="color: #ff7700;">
            <?php echo $token_valido ? 'Redefinir Senha' : 'Token Inválido'; ?>
        </h2>
        
        <?php 
        if (!empty($erro)) {
            echo "<div class='alert alert-danger'>$erro</div>";
        }
        if (!empty($sucesso)) {
            echo "<div class='alert alert-success'>$sucesso</div>";
        }
        ?>
        
        <?php if ($token_valido): ?>
        <form method="post" action="">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            
            <div class="mb-3">
                <label for="nova_senha" class="form-label text-light">Nova Senha</label>
                <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
            </div>
            
            <div class="mb-3">
                <label for="confirmar_senha" class="form-label text-light">Confirmar Nova Senha</label>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Redefinir Senha</button>
        </form>
        <?php endif; ?>
        
        <div class="text-center mt-3">
            <a href="login.php" class="text-light">Voltar para Login</a>
        </div>
    </div>
</body>
</html>