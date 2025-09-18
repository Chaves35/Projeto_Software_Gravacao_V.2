<?php
// Ativar exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Garantir que o conteúdo de resposta seja tratado como JSON
header('Content-Type: application/json');

// Log das requisições (cria diretório se não existir)
$log_dir = '../logs/';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0777, true);
}
file_put_contents(
    $log_dir . 'api_debug.log',
    date('Y-m-d H:i:s') . " - Requisição para adicionar_agendamento.php recebida\n" .
    "POST: " . print_r($_POST, true) . "\n",
    FILE_APPEND
);

// Conectar ao banco de dados (agora com PDO)
require_once('../config/database.php');

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido, apenas POST é aceito']);
    exit;
}

// Recebe e sanitiza os dados do formulário
$professor = isset($_POST['professor']) ? trim($_POST['professor']) : '';
$componente_curricular = isset($_POST['componente_curricular']) ? trim($_POST['componente_curricular']) : '';
$objeto_conhecimento = isset($_POST['objeto_conhecimento']) ? trim($_POST['objeto_conhecimento']) : '';
$estudio_id = isset($_POST['estudio_id']) ? intval(trim($_POST['estudio_id'])) : 0; // Converte para inteiro
$data = isset($_POST['data']) ? trim($_POST['data']) : '';
$hora_inicio = isset($_POST['hora_inicio']) ? trim($_POST['hora_inicio']) : '';
$hora_fim = isset($_POST['hora_fim']) ? trim($_POST['hora_fim']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : 'no-horario';

// Validação básica
if (empty($professor) || empty($componente_curricular) || empty($estudio_id) || empty($data) || empty($hora_inicio) || empty($hora_fim)) {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
    exit;
}

// Validar status
$statusValidos = ['no-horario', 'gravando', 'atrasado', 'cancelado', 'concluido'];
if (!in_array($status, $statusValidos)) {
    echo json_encode(['success' => false, 'message' => 'Status inválido']);
    exit;
}

try {
    // ------------------- PDO Conversion -------------------
    // VERIFICAÇÃO DE CONFLITO DE AGENDAMENTO
    // ------------------------------------------------------
    $sql_check_conflito = "SELECT COUNT(*) FROM estudios_cemeac
                           WHERE estudio_id = :estudio_id
                           AND data = :data
                           AND (hora_inicio < :hora_fim AND hora_fim > :hora_inicio)";
    
    $stmt_check = $pdo->prepare($sql_check_conflito);
    
    $stmt_check->bindParam(':estudio_id', $estudio_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':data', $data, PDO::PARAM_STR);
    $stmt_check->bindParam(':hora_fim', $hora_fim, PDO::PARAM_STR);
    $stmt_check->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
    
    $stmt_check->execute();
    $conflitos = $stmt_check->fetchColumn();
    
    if ($conflitos > 0) {
        $message = "Já existe um agendamento para este estúdio e horário. Por favor, verifique a agenda.";
        file_put_contents(
            $log_dir . 'api_debug.log',
            date('Y-m-d H:i:s') . " - ERRO: $message. Dados: " . json_encode($_POST) . "\n",
            FILE_APPEND
        );
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
    
    // ------------------------------------------------------
    // INSERÇÃO NO BANCO DE DADOS (APENAS SE NÃO HOUVER CONFLITO)
    // ------------------------------------------------------
    file_put_contents(
        $log_dir . 'api_debug.log',
        date('Y-m-d H:i:s') . " - Sem conflitos. Tentando inserir no banco de dados\n",
        FILE_APPEND
    );

    $sql_insert = "INSERT INTO estudios_cemeac
                   (professor, componente_curricular, objeto_conhecimento, estudio_id, data, hora_inicio, hora_fim, status)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_insert = $pdo->prepare($sql_insert);
    
    $stmt_insert->execute([
        $professor,
        $componente_curricular,
        $objeto_conhecimento,
        $estudio_id,
        $data,
        $hora_inicio,
        $hora_fim,
        $status
    ]);
    
    $insert_id = $pdo->lastInsertId();
    
    if ($insert_id) {
        echo json_encode([
            'success' => true,
            'message' => 'Agendamento cadastrado com sucesso',
            'id' => $insert_id
        ]);
        
        file_put_contents(
            $log_dir . 'api_debug.log',
            date('Y-m-d H:i:s') . " - Inserção bem sucedida. ID: $insert_id\n",
            FILE_APPEND
        );
    } else {
        throw new Exception("Erro ao executar a consulta de inserção.");
    }
    
} catch (PDOException $e) {
    file_put_contents(
        $log_dir . 'api_debug.log',
        date('Y-m-d H:i:s') . " - ERRO PDO: " . $e->getMessage() . "\n",
        FILE_APPEND
    );
    
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do banco de dados: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    file_put_contents(
        $log_dir . 'api_debug.log',
        date('Y-m-d H:i:s') . " - ERRO GERAL: " . $e->getMessage() . "\n",
        FILE_APPEND
    );
    
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}

?>