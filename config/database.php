<?php
// Configurações do Banco de Dados
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "estudios";

// Configurações da string de conexão (DSN)
$dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";

// Opções de PDO para lidar com erros e buscar resultados
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Tentar conectar
try {
    // A variável de conexão agora se chama $pdo, como no login.php
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Em caso de erro, exibe a mensagem de erro
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>