<?php
// Define constante de segurança
define('SISTEMA_INTERNO', true);

// Esta página deve ser acessada via AJAX, não diretamente
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(403);
    die("Acesso negado.");
}

// Configurações e conexão com o banco de dados
require_once('../config/config.php');
require_once('../config/url.php');
require_once('../config/database.php'); // Conexão com PDO
date_default_timezone_set('America/Rio_Branco');

$data_hoje = date('Y-m-d');
$agendamentos_html = '';
$professores_nomes = '';

try {
    // ----------------------------------------------------
    // 1. LÓGICA PARA OBTER OS AGENDAMENTOS DO DIA (Tabela)
    // ----------------------------------------------------

    // Consulta SQL com ORDER BY RAND() para randomizar a ordem
    // Nota: O uso de ORDER BY RAND() pode ser lento em tabelas grandes.
    $sql_agendamentos = "
        SELECT
            professor,
            componente_curricular,
            objeto_conhecimento,
            estudio_id,
            data,
            hora_inicio,
            hora_fim,
            status
        FROM
            estudios_cemeac
        WHERE
            data = :data_hoje
        ORDER BY RAND()
    ";

    $stmt_agendamentos = $pdo->prepare($sql_agendamentos);
    $stmt_agendamentos->bindParam(':data_hoje', $data_hoje);
    $stmt_agendamentos->execute();
    $agendamentos = $stmt_agendamentos->fetchAll(PDO::FETCH_ASSOC);

    if ($agendamentos) {
        foreach ($agendamentos as $row) {
            // Mapeamento de status para texto e classe
            $status_label = 'Indefinido'; // Padrão
            $status_class = 'text-muted'; // Padrão, pode ser ajustado com classes de bootstrap como text-secondary

            switch ($row['status']) {
                case 'no-horario':
                    $status_label = 'No Horário';
                    $status_class = 'text-primary'; // Exemplo de classe de texto do Bootstrap
                    break;
                case 'gravando':
                    $status_label = 'Gravando';
                    $status_class = 'text-info';
                    break;
                case 'atrasado':
                    $status_label = 'Atrasado';
                    $status_class = 'text-warning';
                    break;
                case 'cancelado':
                    $status_label = 'Cancelado';
                    $status_class = 'text-danger';
                    break;
                case 'concluido':
                    $status_label = 'Gravação Concluída'; // Ou "Concluída" se preferir
                    $status_class = 'text-success';
                    break;
                // Se houver outros status no DB, adicione-os aqui
            }

            // Constrói a linha da tabela
            // Usei as classes 'text-center' e 'text-status-custom-class' para o <td> do status,
            // mas você pode ajustar o CSS em 'painel.css' para cores de texto ou usar badges
            $agendamentos_html .= '
            <tr>
                <td>' . htmlspecialchars($row['professor']) . '</td>
                <td>' . htmlspecialchars($row['componente_curricular']) . '</td>
                <td>' . htmlspecialchars($row['objeto_conhecimento']) . '</td>
                <td>' . htmlspecialchars($row['estudio_id']) . '</td>
                <td>' . htmlspecialchars($row['data']) . '</td>
                <td>' . htmlspecialchars(substr($row['hora_inicio'], 0, 5)) . ' - ' . htmlspecialchars(substr($row['hora_fim'], 0, 5)) . '</td>
                <td class="text-center ' . $status_class . '">' . $status_label . '</td>
            </tr>';
        }
    } else {
        $agendamentos_html = '<tr><td colspan="7" class="text-center">Nenhum agendamento para hoje.</td></tr>';
    }
    
    // ----------------------------------------------------
    // 2. LÓGICA PARA OBTER OS NOMES DOS PROFESSORES DO DIA (Banner)
    //    Mantido como está, pois não é a causa do problema de status.
    // ----------------------------------------------------

    $sql_professores_hoje = "
        SELECT DISTINCT
            professor
        FROM
            estudios_cemeac
        WHERE
            data = :data_hoje
    ";
    
    $stmt_professores = $pdo->prepare($sql_professores_hoje);
    $stmt_professores->bindParam(':data_hoje', $data_hoje);
    $stmt_professores->execute();
    $professores_hoje = $stmt_professores->fetchAll(PDO::FETCH_COLUMN, 0);

    $professores_nomes = implode(' &bull; ', array_map('htmlspecialchars', $professores_hoje));

    // ----------------------------------------------------
    // 3. PREPARAÇÃO DA RESPOSTA JSON
    // ----------------------------------------------------

    $response = [
        'success' => true,
        'html' => $agendamentos_html,
        'professores' => $professores_nomes // O index.php atual não usa isso, mas mantemos para consistência.
    ];

} catch (PDOException $e) {
    // Em caso de erro, retorna uma resposta de erro
    $response = [
        'success' => false,
        'html' => '<tr><td colspan="7" class="text-center text-danger">Erro ao carregar os dados. Tente novamente mais tarde.</td></tr>',
        'professores' => 'Erro ao carregar os dados.'
    ];
    // Opcional: registrar o erro para depuração (descomente em ambiente de desenvolvimento)
    // error_log('Erro na consulta PDO em obter_agendamentos.php: ' . $e->getMessage());
}

// Retorna a resposta como JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;