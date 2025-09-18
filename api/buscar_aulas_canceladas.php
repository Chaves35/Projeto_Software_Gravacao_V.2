<?php
// Define constante de segurança
define('SISTEMA_INTERNO', true);

session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

require_once('../config/database.php');

header('Content-Type: application/json');

try {
    $professor = isset($_POST['professor']) ? trim($_POST['professor']) : '';
    
    if (empty($professor)) {
        echo json_encode(['success' => false, 'message' => 'Nome do professor é obrigatório']);
        exit();
    }
    
    // Buscar aulas canceladas do professor
    $sql = "SELECT * FROM estudios_cemeac 
            WHERE status = 'cancelado' 
            AND professor LIKE :professor 
            ORDER BY data DESC, hora_inicio DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':professor', "%{$professor}%", PDO::PARAM_STR);
    $stmt->execute();
    
    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'aulas' => $aulas]);
    
} catch (Exception $e) {
    error_log("Erro ao buscar aulas canceladas: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>