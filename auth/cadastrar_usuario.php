<?php
// Define constante de segurança
define('SISTEMA_INTERNO', true);

session_start();
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


// Função para validar cadastro
function validarCadastro($email, $invite_code) {
    $allowed_domains = ['cemeac.edu.br', 'institucional.cemeac.edu.br'];
    $valid_invite_code = 'CEMEAC2025';

    // Verificar domínio
    $email_parts = explode('@', $email);
    $domain = end($email_parts);

    if (!in_array($domain, $allowed_domains)) {
        return "Apenas e-mails institucionais podem se cadastrar";
    }

    // Verificar código de convite
    if ($invite_code !== $valid_invite_code) {
        return "Código de convite inválido";
    }

    return true;
}

// Função para registrar tentativas de cadastro
function registrarTentativaCadastro($email, $sucesso = false) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $data = date('Y-m-d H:i:s');
    
    $log_entry = "[$data] IP: $ip, Email: $email, Status: " . 
                 ($sucesso ? 'SUCESSO' : 'FALHA') . "\n";
    
    // Certifique-se de que o diretório logs exista
    $log_dir = '../logs/';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    
    file_put_contents($log_dir . 'cadastros.log', $log_entry, FILE_APPEND);
}

// Verifica se o usuário já está logado
if (isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Processamento do formulário
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber dados do formulário
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $invite_code = $_POST['invite_code']; // Novo campo

    // Validações
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($invite_code)) {
        $erro = "Todos os campos são obrigatórios.";
    } elseif ($password !== $confirm_password) {
        $erro = "As senhas não coincidem.";
    } elseif (strlen($password) < 8) {
        $erro = "A senha deve ter no mínimo 8 caracteres.";
    } else {
        // Validar cadastro com domínio e código de convite
        $validacao = validarCadastro($email, $invite_code);
        
        if ($validacao !== true) {
            $erro = $validacao;
            registrarTentativaCadastro($email, false);
        } else {
            try {
                // ------------------- PDO Conversion -------------------
                // Verificar se usuário ou email já existem
                $sql_verificacao = "SELECT COUNT(*) FROM usuarios WHERE username = ? OR email = ?";
                $stmt_verificacao = $pdo->prepare($sql_verificacao);
                $stmt_verificacao->execute([$username, $email]);
                
                if ($stmt_verificacao->fetchColumn() > 0) {
                    $erro = "Usuário ou e-mail já cadastrados.";
                    registrarTentativaCadastro($email, false);
                } else {
                    // Hash da senha
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Preparar e executar a inserção
                    $sql_inserir = "INSERT INTO usuarios (username, password, email) VALUES (?, ?, ?)";
                    $stmt_inserir = $pdo->prepare($sql_inserir);
                    $stmt_inserir->execute([$username, $hashed_password, $email]);

                    $sucesso = "Usuário cadastrado com sucesso!";
                    registrarTentativaCadastro($email, true);
                    // Redirecionar para login após 2 segundos
                    header("refresh:2;url=login.php");
                }
            } catch (PDOException $e) {
                // Em caso de erro no banco de dados
                $erro = "Erro ao cadastrar usuário: " . $e->getMessage();
                registrarTentativaCadastro($email, false);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário - CEMEAC</title>
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/painel.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Cadastro de Usuário</h2>
                    </div>
                    <div class="card-body">
                        <?php 
                        // Exibir mensagens de erro ou sucesso
                        if (!empty($erro)) {
                            echo "<div class='alert alert-danger'>$erro</div>";
                        }
                        if (!empty($sucesso)) {
                            echo "<div class='alert alert-success'>$sucesso</div>";
                        }
                        ?>
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nome de Usuário</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail Institucional</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                                <small class="form-text text-muted">Use apenas e-mails com domínio @cemeac.edu.br ou @institucional.cemeac.edu.br</small>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Senha</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="invite_code" class="form-label">Código de Convite</label>
                                <input type="text" class="form-control" id="invite_code" name="invite_code" required>
                                <small class="form-text text-muted">Solicite o código de convite ao administrador</small>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Cadastrar</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $BASE_URL; ?>assets/js/bootstrap.min.js"></script>
</body>
</html>