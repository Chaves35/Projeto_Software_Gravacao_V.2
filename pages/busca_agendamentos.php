<?php
/**
 * Página de Busca de Agendamentos
 *
 * Esta página permite ao usuário pesquisar por agendamentos
 * no banco de dados com base no "Objeto de Conhecimento".
 */

define('SISTEMA_INTERNO', true);
session_start();

// VERIFICAÇÃO DE SEGURANÇA:
// 1. Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php'); // Redireciona para a página de login
    exit();
}

// 2. Verificar se o usuário logado é um administrador
if (!isset($_SESSION['usuario_perfil']) || $_SESSION['usuario_perfil'] !== 'admin') {
    // Redireciona ou exibe uma mensagem de acesso negado
    header('Location: painel-atualizacao.php'); 
    exit();
}

require_once('../config/database.php');
require_once('../config/url.php'); // <-- INCLUSÃO CORRETA DO ARQUIVO url.php

$resultados = [];
$termoPesquisa = '';
$mensagem = '';

// Processar a busca se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['termo_pesquisa'])) {
    $termoPesquisa = trim($_POST['termo_pesquisa']);
    
    if (empty($termoPesquisa)) {
        $mensagem = 'Por favor, digite um termo para a busca.';
    } else {
        try {
            // ------------------- PDO Conversion -------------------
            $sql = "SELECT * FROM estudios_cemeac 
                    WHERE UPPER(objeto_conhecimento) LIKE UPPER(?) 
                    ORDER BY data DESC, hora_inicio ASC";
            
            $stmt = $pdo->prepare($sql);
            $param = '%' . $termoPesquisa . '%';
            $stmt->execute([$param]);

            // Obter todos os resultados de uma vez em um array associativo
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($resultados)) {
                $mensagem = "Nenhum resultado encontrado para '{$termoPesquisa}'.";
            }
            
        } catch (PDOException $e) {
            error_log("Erro na busca: " . $e->getMessage());
            $mensagem = 'Ocorreu um erro ao processar a sua busca.';
        }
    }
}

// Nota: A conexão PDO é fechada automaticamente quando o script termina.

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Agendamentos - CEMEAC</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    
    <link rel="stylesheet" href="<?= $BASE_URL ?>assets/css/painel.css">
</head>
<body>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Buscar Agendamentos</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= $BASE_URL ?>pages/busca_agendamentos.php" class="mb-4">
                            <div class="form-group">
                                <label for="termo_pesquisa">Pesquisar por Objeto de Conhecimento:</label>
                                <input type="text" class="form-control" id="termo_pesquisa" name="termo_pesquisa" placeholder="Digite sua pesquisa." value="<?= htmlspecialchars($termoPesquisa); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Pesquisar
                            </button>
                        </form>
                        
                        <?php if ($mensagem): ?>
                            <div class="alert alert-info mt-3"><?= $mensagem; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($resultados)): ?>
                            <h5 class="mt-4">Resultados da Busca (<?= count($resultados); ?> encontrados)</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Professor(a)</th>
                                            <th>Data</th>
                                            <th>Horário</th>
                                            <th>Estúdio</th>
                                            <th>Status</th>
                                            <th>Objeto de Conhecimento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultados as $agendamento): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($agendamento['id']); ?></td>
                                            <td><?= htmlspecialchars($agendamento['professor']); ?></td>
                                            <td><?= date('d/m/Y', strtotime($agendamento['data'])); ?></td>
                                            <td><?= date('H:i', strtotime($agendamento['hora_inicio'])) . ' - ' . date('H:i', strtotime($agendamento['hora_fim'])); ?></td>
                                            <td>Estúdio <?= htmlspecialchars($agendamento['estudio_id']); ?></td>
                                            <td><?= htmlspecialchars($agendamento['status']); ?></td>
                                            <td><?= htmlspecialchars($agendamento['objeto_conhecimento']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="card-footer text-center">
                        <a href="<?= $BASE_URL ?>pages/painel-atualizacao.php" class="btn btn-outline-secondary">Voltar ao Painel Diário</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>