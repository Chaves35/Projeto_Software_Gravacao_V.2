<?php
session_start();
require_once('../config/config.php'); // Inclui configurações gerais
require_once('../config/database.php'); // Inclui conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtendo dados do formulário
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Verificar se o nome de usuário ou e-mail já existe
    $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Nome de usuário ou e-mail já existe.');</script>";
    } else {
        // Hash da senha
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Inserir novo usuário
        $stmt = $conexao->prepare("INSERT INTO usuarios (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $email);

        if ($stmt->execute()) {
            echo "<script>alert('Cadastro realizado com sucesso! Você pode fazer login agora.');</script>";
            header("Location: login.php"); // Redireciona para a página de login
            exit();
        } else {
            echo "<script>alert('Erro ao cadastrar usuário. Tente novamente.');</script>";
        }
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - CEMEAC Estúdios</title>
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Cadastro de Usuário</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Usuário</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</body>
</html>