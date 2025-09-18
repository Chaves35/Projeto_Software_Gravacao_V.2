<?php
// Define constante de segurança para evitar acesso direto
define('SISTEMA_INTERNO', true);

// Inclui o arquivo de configuração de banco de dados
require_once('../config/database.php');

// Define o cabeçalho para garantir que a resposta seja JSON
header('Content-Type: application/json');

try {
    // Obtém a data atual no formato YYYY-MM-DD
    $data_atual = date('Y-m-d');

    // Consulta para buscar os nomes de professores com agendamento para a data atual
    // Usamos DISTINCT para evitar nomes duplicados caso um professor tenha mais de um agendamento no dia
    $sql = "SELECT DISTINCT professor FROM estudios_cemeac WHERE data = :data_atual";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':data_atual', $data_atual);
    $stmt->execute();
    
    // Obtém todos os resultados em um array
    $professores = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // Constrói o array de resposta no formato JSON esperado pelo JavaScript
    $response = [
        'success' => true,
        'professores' => $professores
    ];

    // Envia a resposta JSON
    echo json_encode($response);

} catch (PDOException $e) {
    // Em caso de erro na conexão ou consulta, envia uma resposta de erro JSON
    $response = [
        'success' => false,
        'message' => 'Erro ao buscar professores: ' . $e->getMessage()
    ];
    echo json_encode($response);
}
?>