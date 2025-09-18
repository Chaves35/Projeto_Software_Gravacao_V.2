<?php
// Iniciar a sessão
session_start();

// Incluir a conexão com o banco de dados (o arquivo config.php não é usado neste script)
require_once('../config/database.php');

// Garantir que a resposta seja JSON
header('Content-Type: application/json');

// Resposta padrão
$response = [
    'success' => false,
    'message' => 'Método de requisição inválido. Apenas POST é permitido.',
];

// Verificar se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter o ID do agendamento a ser excluído
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);

        try {
            // Preparar a instrução SQL para excluir o agendamento
            $stmt = $pdo->prepare("DELETE FROM estudios_cemeac WHERE id = ?");
            
            // Executar a instrução com o array de parâmetros
            $stmt->execute([$id]);

            // Verificar se alguma linha foi afetada para confirmar a exclusão
            if ($stmt->rowCount() > 0) {
                $response['success'] = true;
                $response['message'] = 'Agendamento excluído com sucesso.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Nenhum agendamento encontrado com o ID fornecido ou já foi excluído.';
            }

        } catch (PDOException $e) {
            // Em caso de erro, capturar a exceção e retornar uma mensagem amigável
            $response['message'] = 'Erro ao excluir agendamento: ' . $e->getMessage();
        }
        
    } else {
        $response['message'] = 'ID do agendamento não especificado.';
    }
}

// Enviar a resposta
echo json_encode($response);
?>