<?php
// Define constante de segurança para acesso interno
define('SISTEMA_INTERNO', true);

session_start(); // Inicia a sessão para verificar o perfil do usuário

// ATENÇÃO: Caminhos ajustados para sair da pasta 'api'
require_once('../config/config.php');
require_once('../config/database.php');

header('Content-Type: application/json'); // Garante que a resposta seja JSON

// Verificação de permissão: Apenas admin e administrador podem usar este script
if (!isset($_SESSION['usuario_perfil']) || ($_SESSION['usuario_perfil'] !== 'admin' && $_SESSION['usuario_perfil'] !== 'administrador')) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
    exit();
}

// Verifica se os dados necessários foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $professor = filter_input(INPUT_POST, 'professor', FILTER_SANITIZE_SPECIAL_CHARS);
    $componente_curricular = filter_input(INPUT_POST, 'componente_curricular', FILTER_SANITIZE_SPECIAL_CHARS);
    $objeto_conhecimento = filter_input(INPUT_POST, 'objeto_conhecimento', FILTER_SANITIZE_SPECIAL_CHARS);
    $estudio_id = filter_input(INPUT_POST, 'estudio_id', FILTER_VALIDATE_INT);
    $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_SPECIAL_CHARS);
    $hora_inicio = filter_input(INPUT_POST, 'hora_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
    $hora_fim = filter_input(INPUT_POST, 'hora_fim', FILTER_SANITIZE_SPECIAL_CHARS);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validação básica dos dados
    if (!$id || !$professor || !$componente_curricular || !$objeto_conhecimento || !$estudio_id || !$data || !$hora_inicio || !$hora_fim || !$status) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos ou inválidos.']);
        exit();
    }

    try {
        // Prepara a consulta SQL para atualizar o agendamento
        $sql = "UPDATE estudios_cemeac SET
                    professor = :professor,
                    componente_curricular = :componente_curricular,
                    objeto_conhecimento = :objeto_conhecimento,
                    estudio_id = :estudio_id,
                    data = :data,
                    hora_inicio = :hora_inicio,
                    hora_fim = :hora_fim,
                    status = :status
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        // Binda os parâmetros
        $stmt->bindParam(':professor', $professor);
        $stmt->bindParam(':componente_curricular', $componente_curricular);
        $stmt->bindParam(':objeto_conhecimento', $objeto_conhecimento);
        $stmt->bindParam(':estudio_id', $estudio_id, PDO::PARAM_INT);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fim', $hora_fim);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Executa a consulta
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Agendamento atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Falha ao atualizar agendamento.']);
        }

    } catch (PDOException $e) {
        // Em caso de erro no banco de dados
        error_log("Erro ao atualizar agendamento: " . $e->getMessage()); // Registra o erro
        echo json_encode(['success' => false, 'message' => 'Erro interno do servidor ao atualizar agendamento.']);
    }
} else {
    // Se a requisição não for POST
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
?>