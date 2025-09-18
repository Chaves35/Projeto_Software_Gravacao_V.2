<?php
/**
 * API - Atualizar Status do Agendamento
 * * Este script recebe requisições para atualizar o status de um agendamento
 * e retorna uma resposta JSON com o resultado da operação.
 */

// Adicionada a constante de segurança para consistência com o projeto
define('SISTEMA_INTERNO', true);

// Iniciar a sessão (necessário para verificar o login)
session_start();

// Verificar se o usuário está logado. Se não, encerra a execução por segurança.
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403); // Acesso proibido
    echo json_encode([
        'success' => false,
        'message' => 'Acesso negado. O usuário não está logado.'
    ]);
    exit();
}

// Configurações de cabeçalho para JSON e CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Conectar ao banco de dados (agora com PDO)
require_once('../config/database.php');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Método de requisição inválido. Apenas POST é permitido.'
    ]);
    exit;
}

// Recebe os dados do formulário
$agendamentoId = isset($_POST['id']) ? trim($_POST['id']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

// Validação básica dos dados
if (empty($agendamentoId) || !is_numeric($agendamentoId)) {
    echo json_encode([
        'success' => false, 
        'message' => 'ID de agendamento inválido ou não informado.',
        'debug' => ['id_recebido' => $agendamentoId]
    ]);
    exit;
}

// Converter para inteiro para garantir que é um número
$agendamentoId = intval($agendamentoId);

// Validar status
$statusValidos = ['no-horario', 'gravando', 'atrasado', 'cancelado', 'concluido'];
if (!in_array($status, $statusValidos)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Status inválido. Valores permitidos: ' . implode(', ', $statusValidos),
        'debug' => ['status_recebido' => $status]
    ]);
    exit;
}

try {
    // ------------------- PDO Conversion -------------------
    // Verificar se o agendamento existe e obter o status atual
    // A consulta foi alterada para `SELECT` para usar PDO
    $sqlVerificar = "SELECT id, status FROM estudios_cemeac WHERE id = ?";
    $stmtVerificar = $pdo->prepare($sqlVerificar);
    $stmtVerificar->execute([$agendamentoId]);
    $agendamento = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

    if (!$agendamento) {
        echo json_encode([
            'success' => false, 
            'message' => 'Agendamento não encontrado com o ID: ' . $agendamentoId
        ]);
        exit;
    }
    
    // Se o status atual já for o mesmo que estamos tentando atualizar
    if ($agendamento['status'] === $status) {
        echo json_encode([
            'success' => true, 
            'message' => 'O status já está atualizado como: ' . $status,
            'agendamento_id' => $agendamentoId,
            'no_change' => true
        ]);
        exit;
    }
    
    // Atualizar o status no banco de dados
    $sql = "UPDATE estudios_cemeac SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $agendamentoId]);

    // Verificar se alguma linha foi afetada usando rowCount()
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Status atualizado com sucesso', 
            'agendamento_id' => $agendamentoId,
            'old_status' => $agendamento['status'],
            'new_status' => $status
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Nenhuma alteração foi realizada. Status pode ser o mesmo do atual.'
        ]);
    }
    
} catch (PDOException $e) {
    // Tratamento de erro específico para PDO
    error_log('Erro PDO ao atualizar status: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro interno do banco de dados: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Tratamento de erro geral
    error_log('Erro geral ao atualizar status: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao processar a requisição: ' . $e->getMessage()
    ]);
}

?>