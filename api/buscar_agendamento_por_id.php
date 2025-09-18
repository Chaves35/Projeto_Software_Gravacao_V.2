<?php
// Define constante de segurança para acesso interno
define('SISTEMA_INTERNO', true);

// ATENÇÃO: Caminhos ajustados para sair da pasta 'api'
require_once('../config/config.php');
require_once('../config/database.php');

header('Content-Type: application/json'); // Garante que a resposta seja JSON

// Verificar se o ID do agendamento foi fornecido via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do agendamento não fornecido.']);
    exit();
}

$agendamento_id = intval($_GET['id']); // Converte para inteiro por segurança

try {
    // Consulta para obter os detalhes do agendamento
    $sql = "SELECT * FROM estudios_cemeac WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $agendamento_id, PDO::PARAM_INT);
    $stmt->execute();

    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($agendamento) {
        echo json_encode(['success' => true, 'agendamento' => $agendamento]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Agendamento não encontrado.']);
    }

} catch (PDOException $e) {
    // Em caso de erro no banco de dados
    error_log("Erro ao buscar agendamento: " . $e->getMessage()); // Registra o erro para depuração
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor ao buscar agendamento.']);
}
?>