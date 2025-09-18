<?php
require_once('../config/database.php');

// Data atual
$data_atual = date('Y-m-d');

// Consulta para buscar professores do dia
$sql = "SELECT DISTINCT professor FROM estudios_cemeac WHERE data = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $data_atual);
$stmt->execute();
$result = $stmt->get_result();

$professores = [];
while ($row = $result->fetch_assoc()) {
    $professores[] = [
        'nome' => htmlspecialchars($row['professor'])
    ];
}

header('Content-Type: application/json');
echo json_encode([
    'data' => $data_atual,
    'professores' => $professores
]);

$stmt->close();
$conexao->close();
?>