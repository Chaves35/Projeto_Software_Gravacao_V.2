<?php
// Iniciar sessão apenas se não estiver ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('../config/config.php');
require_once('../config/database.php');

// Processamento do login
// A variável de erro é inicializada aqui para ser usada tanto no PHP quanto no HTML
$login_erro = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Agora pegamos o valor do campo "email_ou_usuario"
    $input_login = $_POST['email_ou_usuario'] ?? '';
    // CORRIGIDO: Pegamos o valor do campo "senha"
    $senha = $_POST['senha'] ?? '';

    // Verifica se os campos não estão vazios
    if (!empty($input_login) && !empty($senha)) {
        // Lógica de autenticação com PDO para buscar por username ou email
        $sql = "SELECT id, username, password, perfil, studio_responsavel, email FROM usuarios WHERE username = ? OR email = ?";
        
        // Verificação para garantir que o objeto PDO existe
        if (isset($pdo) && $pdo instanceof PDO) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$input_login, $input_login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica se um usuário foi encontrado e se a senha está correta
            if ($user && password_verify($senha, $user['password'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['usuario_perfil'] = $user['perfil'];
                $_SESSION['studio_responsavel'] = $user['studio_responsavel'];

                // Redireciona para a página do painel
header("Location: " . $BASE_URL . "pages/painel-atualizacao.php");
exit();
                exit();
            } else {
                // Mensagem de erro unificada para ser mais segura
                $login_erro = "Usuário, e-mail ou senha incorretos.";
            }
        } else {
            $login_erro = "Erro: A conexão com o banco de dados não foi estabelecida.";
        }
    } else {
        $login_erro = "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CEMEAC Estúdios</title>

    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    body {
        background-color: #121212;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        font-family: 'Arial', sans-serif;
    }

    .login-wrapper {
        display: flex;
        width: 100%;
        max-width: 500px;
        background-color: #1f1f1f;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    .login-sidebar {
        width: 10px;
        background-color: #ff7700;
    }

    .login-container {
        flex-grow: 1;
        padding: 30px;
        background-color: #1f1f1f;
    }

    .login-title {
        color: #ff7700;
        text-align: center;
        margin-bottom: 25px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .form-control {
        background-color: #121212;
        border: 1px solid #333;
        color: #e0e0e0;
    }

    .form-control:focus {
        background-color: #121212;
        border-color: #ff7700;
        box-shadow: none;
        color: #e0e0e0;
    }

    .btn-primary {
        background-color: #ff7700;
        border-color: #ff7700;
        width: 100%;
        padding: 10px;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #e66c00;
        border-color: #e66c00;
    }

    .login-footer {
        text-align: center;
        margin-top: 20px;
        color: #888;
    }

    .login-footer a {
        color: #ff7700;
        text-decoration: none;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }

    .password-recovery {
        color: #999;
        text-align: center;
        margin-top: 15px;
        font-size: 0.9em;
    }

    .password-recovery a {
        color: #ff7700;
        text-decoration: none;
    }

    .password-recovery a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-sidebar"></div>
        <div class="login-container">
            <h2 class="login-title">Login do Sistema</h2>

            <?php 
            // CORRIGIDO: Agora exibimos a variável de erro correta
            if(!empty($login_erro)) {
                echo "<div class='alert alert-danger'>$login_erro</div>";
            }
            ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="mb-3">
                    <label for="email_ou_usuario" class="form-label text-light">EMAIL OU USUÁRIO</label>
                    <input type="text" class="form-control" id="email_ou_usuario" name="email_ou_usuario" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label text-light">SENHA</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>ENTRAR
                </button>
                
            </form>

            <div class="login-footer">
                <p class="mt-3">
                    Não tem uma conta?
                    <a href="<?php echo $BASE_URL; ?>auth/cadastrar_usuario.php">Cadastre-se</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo $BASE_URL; ?>assets/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>

</html>