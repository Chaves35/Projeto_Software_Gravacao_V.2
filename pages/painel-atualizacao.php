<?php
// Define constante de segurança
define('SISTEMA_INTERNO', true);

session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ATENÇÃO: Caminhos ajustados para sair da pasta 'pages'
require_once('../config/config.php');
require_once('../config/url.php');
require_once('../config/database.php');

// Obtendo a data atual
$data_atual = date('Y-m-d');

// Parâmetros de paginação
$registros_por_pagina = 10; // Número de registros por página
$pagina_atual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Consulta para obter o total de registros (usando PDO)
$sql_total = "SELECT COUNT(*) FROM estudios_cemeac";
$stmt_total = $pdo->prepare($sql_total);
$stmt_total->execute();
$total_registros = $stmt_total->fetchColumn();

// Calcular total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Atualização - CEMEAC</title>

    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/painel.css">
    <link rel="shortcut icon" href="<?php echo $BASE_URL; ?>assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand">
                <i class="fas fa-tachometer-alt me-2"></i>Painel Administrativo
            </a>

            <?php
            // Botão "Buscar Agendamentos" visível para 'admin' OU 'administrador'
            if (isset($_SESSION['usuario_perfil']) && ($_SESSION['usuario_perfil'] === 'admin' || $_SESSION['usuario_perfil'] === 'administrador')) :
            ?>
                <a href="<?= $BASE_URL ?>pages/busca_agendamentos.php" class="btn btn-secondary">
                    <i class="fas fa-search"></i> Buscar Agendamentos
                </a>
            <?php endif; ?>

            <div class="nav-buttons">
                <a href="<?php echo $BASE_URL; ?>index.php" class="btn btn-info me-2">
                    <i class="fas fa-home me-1"></i>Página Inicial
                </a>
                <a href="<?php echo $BASE_URL; ?>auth/logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-1"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2>Gerenciamento de Agendamentos</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adicionarAgendamentoModal">
                    <i class="fas fa-plus-circle me-2"></i>Novo Agendamento
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>PROFESSOR(A)</th>
                            <th>COMPONENTE</th>
                            <th>OBJETO DE CONHECIMENTO</th>
                            <th>ESTÚDIO</th>
                            <th>DATA</th>
                            <th>HORÁRIO</th>
                            <th>STATUS</th>
                            <th>AÇÃO</th>
                        </tr>
                    </thead>
                    <tbody id="agendamentos-tbody">
                        <?php
                        // Consulta paginada para obter agendamentos
                        $sql = "SELECT * FROM estudios_cemeac ORDER BY data DESC, hora_inicio DESC LIMIT :limit OFFSET :offset";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':limit', $registros_por_pagina, PDO::PARAM_INT);
                        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                        $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($agendamentos) {
                            foreach ($agendamentos as $row) {
                                // Mapeamento de status para classes de badge e texto
                                $status_badge_class = 'badge-secondary'; // Default
                                $status_text = ucfirst($row['status']);

                                switch ($row['status']) {
                                    case 'no-horario':
                                        $status_badge_class = 'bg-success';
                                        $status_text = 'No Horário';
                                        break;
                                    case 'gravando':
                                        $status_badge_class = 'bg-info';
                                        $status_text = 'Gravando';
                                        break;
                                    case 'atrasado':
                                        $status_badge_class = 'bg-warning text-dark';
                                        $status_text = 'Atrasado';
                                        break;
                                    case 'cancelado':
                                        $status_badge_class = 'bg-danger';
                                        $status_text = 'Cancelado';
                                        break;
                                    case 'concluido':
                                        $status_badge_class = 'bg-success';
                                        $status_text = 'Gravação Concluída';
                                        break;
                                }

                                // --- INÍCIO DO AJUSTE: Mapeamento do ID do estúdio para o nome amigável ---
                                $nome_estudio = htmlspecialchars($row['estudio_id']); // Padrão, caso não encontre
                                switch ($row['estudio_id']) {
                                    case '1':
                                        $nome_estudio = 'Estúdio 1';
                                        break;
                                    case '2':
                                        $nome_estudio = 'Estúdio 2';
                                        break;
                                    case '3':
                                        $nome_estudio = 'Estúdio 3';
                                        break;
                                    case '4':
                                        $nome_estudio = 'Estúdio 4';
                                        break;
                                    case '5': // NOVO: Gravações Externas
                                        $nome_estudio = 'Gravações Externas';
                                        break;
                                }
                                // --- FIM DO AJUSTE ---

                                echo '<tr>
                                        <td>' . htmlspecialchars($row['professor']) . '</td>
                                        <td>' . htmlspecialchars($row['componente_curricular']) . '</td>
                                        <td>' . htmlspecialchars($row['objeto_conhecimento']) . '</td>
                                        <td>' . $nome_estudio . '</td> <td>' . htmlspecialchars($row['data']) . '</td>
                                        <td>' . htmlspecialchars($row['hora_inicio']) . ' - ' . htmlspecialchars($row['hora_fim']) . '</td>
                                        <td><span class="badge rounded-pill ' . $status_badge_class . '">' . $status_text . '</span></td>
                                        <td>'; // <--- TD de AÇÃO começa aqui

                                // Adicionar o botão de Reagendamento/Edição apenas para admin/administrador
                                if (isset($_SESSION['usuario_perfil']) && ($_SESSION['usuario_perfil'] === 'admin' || $_SESSION['usuario_perfil'] === 'administrador')) {
                                    echo '
                                                <button class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#reagendamentoModal" data-id="' . $row['id'] . '">
                                                    <i class="fas fa-calendar-alt"></i> </button>
                                            ';
                                }
                                echo '
                                            <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="confirmarExclusao(' . $row['id'] . ')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="8" class="text-center">Nenhum agendamento encontrado.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>

                <nav aria-label="Navegação de páginas">
                    <ul class="pagination justify-content-center">
                        <?php
                        // Botão Anterior
                        if ($pagina_atual > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?pagina=' . ($pagina_atual - 1) . '">Anterior</a></li>';
                        }

                        // Números de Página
                        $inicio = max(1, $pagina_atual - 2);
                        $fim = min($total_paginas, $pagina_atual + 2);

                        if ($inicio > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?pagina=1">1</a></li>';
                            if ($inicio > 2) {
                                echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                            }
                        }

                        for ($i = $inicio; $i <= $fim; $i++) {
                            echo '<li class="page-item ' . ($pagina_atual == $i ? 'active' : '') . '"><a class="page-link" href="?pagina=' . $i . '">' . $i . '</a></li>';
                        }

                        if ($fim < $total_paginas) {
                            if ($fim < $total_paginas - 1) {
                                echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="?pagina=' . $total_paginas . '">' . $total_paginas . '</a></li>';
                        }

                        // Botão Próximo
                        if ($pagina_atual < $total_paginas) {
                            echo '<li class="page-item"><a class="page-link" href="?pagina=' . ($pagina_atual + 1) . '">Próximo</a></li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- MODAL ATUALIZADO: Novo Agendamento com Sistema de Busca -->
    <div class="modal fade" id="adicionarAgendamentoModal" tabindex="-1" aria-labelledby="adicionarAgendamentoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="adicionarAgendamentoLabel">
                        <i class="bi bi-calendar-plus me-2"></i>Novo Agendamento de Gravação
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Abas de navegação -->
                    <ul class="nav nav-tabs" id="agendamentoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="novo-tab" data-bs-toggle="tab" data-bs-target="#novo-agendamento" 
                                    type="button" role="tab" aria-controls="novo-agendamento" aria-selected="true">
                                <i class="fas fa-plus me-1"></i>Novo Agendamento
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="buscar-cancelados-tab" data-bs-toggle="tab" data-bs-target="#buscar-cancelados" 
                                    type="button" role="tab" aria-controls="buscar-cancelados" aria-selected="false">
                                <i class="fas fa-search me-1"></i>Reagendar Cancelados
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="agendamentoTabsContent">
                        <!-- Aba: Novo Agendamento -->
                        <div class="tab-pane fade show active" id="novo-agendamento" role="tabpanel" aria-labelledby="novo-tab">
                            <form id="formNovoAgendamento" method="post" class="mt-3"
                                action="<?php echo $BASE_URL; ?>api/adicionar_agendamento.php">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="professor" class="form-label">Professor(a)</label>
                                        <input type="text" class="form-control" id="professor" name="professor" required>
                                        <div class="invalid-feedback">Por favor, informe o nome do professor.</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="componente_curricular" class="form-label">Componente Curricular</label>
                                        <input type="text" class="form-control" id="componente_curricular"
                                            name="componente_curricular" required>
                                        <small class="form-text text-muted">Digite o nome do componente curricular</small>
                                        <div class="invalid-feedback">Por favor, informe o componente curricular.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="objeto_conhecimento" class="form-label">Objeto de Conhecimento</label>
                                        <input type="text" class="form-control" id="objeto_conhecimento"
                                            name="objeto_conhecimento" required>
                                        <small class="form-text text-muted">Digite o objeto de conhecimento específico para esta
                                            gravação</small>
                                        <div class="invalid-feedback">Por favor, informe o objeto de conhecimento.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="estudio_id" class="form-label">Estúdio</label>
                                        <select class="form-select" id="estudio_id" name="estudio_id" required>
                                            <option value="">Selecione o Estúdio</option>
                                            <option value="1">Estúdio 1</option>
                                            <option value="2">Estúdio 2</option>
                                            <option value="3">Estúdio 3</option>
                                            <option value="4">Estúdio 4</option>
                                            <option value="5">Gravações Externas</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione um estúdio.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="data" class="form-label"><i class="bi bi-calendar-date me-2"></i>Data da
                                            Gravação</label>
                                        <input type="date" class="form-control" id="data" name="data" required>
                                        <div class="invalid-feedback">Por favor, selecione a data da gravação.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="status" class="form-label">Status Inicial</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="no-horario">No Horário</option>
                                            <option value="gravando">Gravando</option>
                                            <option value="atrasado">Atrasado</option>
                                            <option value="cancelado">Cancelado</option>
                                            <option value="concluido">Gravação Concluída</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione o status inicial.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="hora_inicio" class="form-label"><i class="bi bi-clock me-2"></i>Hora de
                                            Início</label>
                                        <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                                        <div class="invalid-feedback">Por favor, informe a hora de início.</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="hora_fim" class="form-label"><i class="bi bi-clock-fill me-2"></i>Hora de
                                            Término</label>
                                        <input type="time" class="form-control" id="hora_fim" name="hora_fim" required>
                                        <div class="invalid-feedback">Por favor, informe a hora de término.</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save me-2"></i>Salvar Agendamento
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Aba: Buscar Aulas Canceladas -->
                        <div class="tab-pane fade" id="buscar-cancelados" role="tabpanel" aria-labelledby="buscar-cancelados-tab">
                            <div class="mt-3">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label for="busca-professor" class="form-label">
                                            <i class="fas fa-search me-2"></i>Buscar por Professor
                                        </label>
                                        <input type="text" class="form-control" id="busca-professor" 
                                               placeholder="Digite o nome do professor para buscar aulas canceladas...">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary w-100" onclick="buscarAulasCanceladas()">
                                            <i class="fas fa-search me-2"></i>Buscar
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="resultados-busca" class="mt-3">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-search fa-2x mb-2"></i>
                                        <p>Digite o nome do professor e clique em "Buscar" para encontrar aulas canceladas.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="statusModalLabel">Atualizar Status do Agendamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formAtualizarStatus" action="<?php echo $BASE_URL; ?>api/atualizar_status.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="agendamentoId" name="id">
                        <div class="mb-3">
                            <label for="novoStatus" class="form-label">Novo Status</label>
                            <select class="form-select" id="novoStatus" name="status">
                                <option value="no-horario">No Horário</option>
                                <option value="gravando">Gravando</option>
                                <option value="atrasado">Atrasado</option>
                                <option value="cancelado">Cancelado</option>
                                <option value="concluido">Gravação Concluída</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Salvar Mudanças</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reagendamentoModal" tabindex="-1" aria-labelledby="reagendamentoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary ">
                    <h5 class="modal-title text-white" id="reagendamentoModalLabel">
                        <i class="fas fa-calendar-check me-2 text-white"></i>Reagendar/Editar Agendamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formReagendamento" method="post"
                        action="<?php echo $BASE_URL; ?>api/reagendar_agendamento.php">
                        <input type="hidden" id="reagendamentoId" name="id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reag_professor" class="form-label">Professor(a)</label>
                                <input type="text" class="form-control" id="reag_professor" name="professor" required>
                                <div class="invalid-feedback">Por favor, informe o nome do professor.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="reag_componente_curricular" class="form-label">Componente Curricular</label>
                                <input type="text" class="form-control" id="reag_componente_curricular"
                                    name="componente_curricular" required>
                                <small class="form-text text-muted">Digite o nome do componente curricular</small>
                                <div class="invalid-feedback">Por favor, informe o componente curricular.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="reag_objeto_conhecimento" class="form-label">Objeto de Conhecimento</label>
                                <input type="text" class="form-control" id="reag_objeto_conhecimento"
                                    name="objeto_conhecimento" required>
                                <small class="form-text text-muted">Digite o objeto de conhecimento específico para esta
                                    gravação</small>
                                <div class="invalid-feedback">Por favor, informe o objeto de conhecimento.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="reag_estudio_id" class="form-label">Estúdio</label>
                                <select class="form-select" id="reag_estudio_id" name="estudio_id" required>
                                    <option value="">Selecione o Estúdio</option>
                                    <option value="1">Estúdio 1</option>
                                    <option value="2">Estúdio 2</option>
                                    <option value="3">Estúdio 3</option>
                                    <option value="4">Estúdio 4</option>
                                    <option value="5">Gravações Externas</option> </select>
                                <div class="invalid-feedback">Por favor, selecione um estúdio.</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="reag_data" class="form-label"><i class="fas fa-calendar-alt me-2"></i>Data
                                    da Gravação</label>
                                <input type="date" class="form-control" id="reag_data" name="data" required>
                                <div class="invalid-feedback">Por favor, selecione a data da gravação.</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="reag_status" class="form-label">Status</label>
                                <select class="form-select" id="reag_status" name="status" required>
                                    <option value="no-horario">No Horário</option>
                                    <option value="gravando">Gravando</option>
                                    <option value="atrasado">Atrasado</option>
                                    <option value="cancelado">Cancelado</option>
                                    <option value="concluido">Gravação Concluída</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o status.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reag_hora_inicio" class="form-label"><i class="fas fa-clock me-2"></i>Hora
                                    de Início</label>
                                <input type="time" class="form-control" id="reag_hora_inicio" name="hora_inicio"
                                    required>
                                <div class="invalid-feedback">Por favor, informe a hora de início.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="reag_hora_fim" class="form-label"><i
                                        class="fas fa-hourglass-end me-2"></i>Hora de Término</label>
                                <input type="time" class="form-control" id="reag_hora_fim" name="hora_fim" required>
                                <div class="invalid-feedback">Por favor, informe a hora de término.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="fas fa-times-circle me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo $BASE_URL; ?>assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Definição da URL base para uso nos scripts
        const BASE_URL = '<?php echo $BASE_URL; ?>';

        $(document).ready(function () {
            // Manipula o envio do formulário de novo agendamento
            $('#formNovoAgendamento').on('submit', function (event) {
                event.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: BASE_URL + 'api/adicionar_agendamento.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Agendamento cadastrado com sucesso!');
                            $('#adicionarAgendamentoModal').modal('hide');
                            window.location.reload();
                        } else {
                            alert('Erro: ' + (response.message || 'Falha ao cadastrar agendamento'));
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro na requisição AJAX:", status, error, xhr.responseText);
                        alert('Erro na comunicação com o servidor. Verifique o console para mais detalhes.');
                    }
                });
            });

            // Preenche o modal de atualização de status com os dados do agendamento
            $('#statusModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const status = button.data('status');

                const modal = $(this);
                modal.find('#agendamentoId').val(id);
                modal.find('#novoStatus').val(status);
            });

            // Manipula o envio do formulário de atualização de status
            $('#formAtualizarStatus').on('submit', function (event) {
                event.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: BASE_URL + 'api/atualizar_status.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Status atualizado com sucesso!');
                            $('#statusModal').modal('hide');
                            window.location.reload();
                        } else {
                            alert('Erro ao atualizar status: ' + (response.message || 'Falha desconhecida.'));
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro na requisição AJAX:", status, error, xhr.responseText);
                        alert('Erro na comunicação com o servidor. Verifique o console para mais detalhes.');
                    }
                });
            });

            // Preenche o modal de reagendamento/edição com os dados do agendamento
            $('#reagendamentoModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget); // Botão que acionou o modal
                const agendamentoId = button.data('id'); // Extrai o ID do agendamento do botão

                // Requisição AJAX para buscar os detalhes do agendamento
                $.ajax({
                    url: BASE_URL + 'api/buscar_agendamento_por_id.php',
                    type: 'GET',
                    data: { id: agendamentoId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success && response.agendamento) {
                            const agendamento = response.agendamento;
                            const modal = $('#reagendamentoModal');

                            // Preenche os campos do formulário no modal
                            modal.find('#reagendamentoId').val(agendamento.id);
                            modal.find('#reag_professor').val(agendamento.professor);
                            modal.find('#reag_componente_curricular').val(agendamento.componente_curricular);
                            modal.find('#reag_objeto_conhecimento').val(agendamento.objeto_conhecimento);
                            
                            // --- INÍCIO DO AJUSTE: Pré-seleciona o estúdio no modal de edição ---
                            modal.find('#reag_estudio_id').val(agendamento.estudio_id);
                            // --- FIM DO AJUSTE ---
                            
                            modal.find('#reag_data').val(agendamento.data);
                            modal.find('#reag_hora_inicio').val(agendamento.hora_inicio);
                            modal.find('#reag_hora_fim').val(agendamento.hora_fim);
                            modal.find('#reag_status').val(agendamento.status); // Preenche o status atual
                        } else {
                            alert('Erro ao carregar dados do agendamento: ' + (response.message || 'Agendamento não encontrado.'));
                            $('#reagendamentoModal').modal('hide'); // Fecha o modal se houver erro
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro na requisição AJAX para carregar agendamento:", status, error, xhr.responseText);
                        alert('Erro ao carregar dados do agendamento. Verifique o console para mais detalhes.');
                        $('#reagendamentoModal').modal('hide'); // Fecha o modal em caso de erro
                    }
                });
            });

            // Manipula o envio do formulário de reagendamento/edição
            $('#formReagendamento').on('submit', function (event) {
                event.preventDefault(); // Impede o envio padrão do formulário
                const formData = $(this).serialize(); // Serializa os dados do formulário

                $.ajax({
                    url: BASE_URL + 'api/reagendar_agendamento.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Agendamento atualizado com sucesso!');
                            $('#reagendamentoModal').modal('hide'); // Fecha o modal
                            window.location.reload(); // Recarrega a página para ver as mudanças
                        } else {
                            alert('Erro ao atualizar agendamento: ' + (response.message || 'Falha desconhecida.'));
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro na requisição AJAX para atualizar agendamento:", status, error, xhr.responseText);
                        alert('Erro na comunicação com o servidor. Verifique o console para mais detalhes.');
                    }
                });
            });

            // --- NOVAS FUNÇÕES PARA BUSCA DE AULAS CANCELADAS ---

            // Função para buscar aulas canceladas
            window.buscarAulasCanceladas = function() {
                const professor = $('#busca-professor').val().trim();
                
                if (professor === '') {
                    $('#resultados-busca').html(`
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Por favor, digite o nome do professor para buscar.
                        </div>
                    `);
                    return;
                }
                
                // Mostrar loading
                $('#resultados-busca').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Buscando...</span>
                        </div>
                        <p class="mt-2">Buscando aulas canceladas...</p>
                    </div>
                `);
                
                $.ajax({
                    url: BASE_URL + 'api/buscar_aulas_canceladas.php',
                    type: 'POST',
                    data: { professor: professor },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            if (response.aulas.length > 0) {
                                let html = `
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Encontradas ${response.aulas.length} aula(s) cancelada(s) para "${professor}".
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Professor</th>
                                                    <th>Componente</th>
                                                    <th>Objeto de Conhecimento</th>
                                                    <th>Estúdio</th>
                                                    <th>Data</th>
                                                    <th>Horário</th>
                                                    <th>Ação</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;
                                
                                response.aulas.forEach(function(aula) {
                                    // Mapear ID do estúdio para nome
                                    let nomeEstudio = 'Estúdio ' + aula.estudio_id;
                                    if (aula.estudio_id === '5') {
                                        nomeEstudio = 'Gravações Externas';
                                    }
                                    
                                    html += `
                                        <tr>
                                            <td>${aula.professor}</td>
                                            <td>${aula.componente_curricular}</td>
                                            <td>${aula.objeto_conhecimento}</td>
                                            <td>${nomeEstudio}</td>
                                            <td>${formatarData(aula.data)}</td>
                                            <td>${aula.hora_inicio} - ${aula.hora_fim}</td>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm" 
                                                        onclick="preencherFormularioReagendamento(${aula.id})">
                                                    <i class="fas fa-calendar-plus me-1"></i>Reagendar
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                });
                                
                                html += `
                                            </tbody>
                                        </table>
                                    </div>
                                `;
                                
                                $('#resultados-busca').html(html);
                            } else {
                                $('#resultados-busca').html(`
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Nenhuma aula cancelada encontrada para o professor "${professor}".
                                    </div>
                                `);
                            }
                        } else {
                            $('#resultados-busca').html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Erro ao buscar aulas: ${response.message}
                                </div>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro na busca:", status, error);
                        $('#resultados-busca').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Erro na comunicação com o servidor.
                            </div>
                        `);
                    }
                });
            };

            // Função para preencher o formulário com dados da aula cancelada
            window.preencherFormularioReagendamento = function(aulaId) {
                $.ajax({
                    url: BASE_URL + 'api/buscar_agendamento_por_id.php',
                    type: 'GET',
                    data: { id: aulaId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.agendamento) {
                            const aula = response.agendamento;
                            
                            // Mudar para a aba de novo agendamento
                            $('#novo-tab').tab('show');
                            
                            // Preencher os campos do formulário
                            $('#professor').val(aula.professor);
                            $('#componente_curricular').val(aula.componente_curricular);
                            $('#objeto_conhecimento').val(aula.objeto_conhecimento);
                            $('#estudio_id').val(aula.estudio_id);
                            
                            // Limpar data e horários para nova marcação
                            $('#data').val('');
                            $('#hora_inicio').val('');
                            $('#hora_fim').val('');
                            $('#status').val('no-horario');
                            
                            // Mostrar alerta de sucesso
                            Swal.fire({
                                icon: 'success',
                                title: 'Dados Preenchidos!',
                                text: 'Os dados da aula cancelada foram preenchidos. Defina a nova data e horário.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert('Erro ao carregar dados da aula: ' + (response.message || 'Aula não encontrada.'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao carregar aula:", status, error);
                        alert('Erro ao carregar dados da aula.');
                    }
                });
            };

            // Função auxiliar para formatar data
            function formatarData(data) {
                if (!data) return '';
                const partes = data.split('-');
                if (partes.length === 3) {
                    return `${partes[2]}/${partes[1]}/${partes[0]}`;
                }
                return data;
            }

            // Permitir busca ao pressionar Enter
            $('#busca-professor').on('keypress', function(e) {
                if (e.which === 13) { // Enter
                    buscarAulasCanceladas();
                }
            });

            // Função para confirmar exclusão
            window.confirmarExclusao = function(id) {
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Você não poderá reverter isso!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: BASE_URL + 'api/excluir_agendamento.php',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Excluído!',
                                        'O agendamento foi excluído.',
                                        'success'
                                    ).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Erro!',
                                        'Não foi possível excluir o agendamento: ' + (response.message || 'Erro desconhecido.'),
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Erro na requisição AJAX de exclusão:", status, error, xhr.responseText);
                                Swal.fire(
                                    'Erro!',
                                    'Erro na comunicação com o servidor.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            };
        });
    </script>
</body>

</html>