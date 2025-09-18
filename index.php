<?php
// Define constante de segurança
define('SISTEMA_INTERNO', true);

session_start();
require_once('config/config.php'); // Configurações gerais
require_once('config/url.php'); // URL base do projeto
require_once('config/database.php'); // Conexão com o banco de dados
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sistema de Gerenciamento de Estúdios - CEMEAC</title>

    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/painel.css">
    <link rel="shortcut icon" href="<?php echo $BASE_URL; ?>assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <style>
    /* Estilos para o scroller de professores */
    .professores-scroller {
        overflow: hidden;
        white-space: nowrap;
        flex-grow: 1;
        display: flex; /* Torna o scroller um flex container */
        align-items: center; /* Centraliza verticalmente o conteúdo dentro do scroller (os nomes) */
        line-height: 1.2em; /* Define uma altura de linha consistente para os nomes */
    }

    .professores-scroller .professores-list {
        display: inline-block;
        padding-right: 100%;
        /* Removendo line-height e vertical-align aqui, o flexbox pai agora cuida */
    }

    .professores-scroller .professor-name {
        display: inline-block;
        padding: 0 5px;
        color: #fff;
        font-size: 1.4em;
        /* Removendo line-height e vertical-align aqui, o flexbox pai agora cuida */
    }

    .professores-scroller .separator {
        margin: 0 10px;
        color: rgba(255, 255, 255, 0.7);
        /* Removendo line-height e vertical-align aqui, o flexbox pai agora cuida */
    }

    /* AJUSTE CHAVE: Centraliza o texto DENTRO do h4 e padroniza line-height */
    .card-header h4 {
        display: flex; /* Torna o h4 um flex container */
        align-items: center; /* Centraliza verticalmente o conteúdo de "Professores de Hoje:" */
        line-height: 1.2em; /* Define uma altura de linha consistente para o texto do h4 */
        /* As classes Bootstrap 'my-0' e 'me-3' já cuidam das margens */
    }
    
    /* No seu arquivo CSS (ou dentro de <style> no index.php) */

/* ----------- ESTILOS PARA O RODAPÉ E LOGO ----------- */

/* Estilos para o rodapé geral */
.footer {
    position: relative; /* CRUCIAL: Permite posicionamento absoluto de elementos filhos */
    min-height: 70px;   /* Altura mínima do rodapé para a logo ter espaço. Ajuste conforme a altura da sua logo + padding. */
    background-color: #212529 !important; /* Cor preta. Use a cor exata do seu fundo se for diferente */
    padding: 0;         /* Remove padding padrão do Bootstrap, se houver */
    display: flex;      /* Para alinhamento, se necessário */
    align-items: center; /* Alinha verticalmente a logo */
    justify-content: flex-start; /* Alinha horizontalmente ao início (esquerda) */
    /* Se o seu rodapé precisa ficar "colado" na parte inferior da tela,
       mesmo com pouco conteúdo na página, você pode precisar de:
       margin-top: auto;
       width: 100%;
    */
}

/* Estilos para o container da logo no rodapé */
.logo-cemeac-footer {
    position: absolute; /* Posiciona a logo em relação ao rodapé pai */
    bottom: 15px; /* Distância do fundo do rodapé. Ajuste conforme necessário. */
    left: 20px;   /* Distância inicial da esquerda do rodapé. Ajuste conforme necessário. */
    
    width: 100px; /* Largura inicial da logo. Ajuste conforme necessário. */
    height: auto;
    
    /* ADICIONA AS LINHAS PARA A ANIMAÇÃO AUTOMÁTICA DE BALANÇO HORIZONTAL */
    animation: horizontalSwing 4s ease-in-out infinite alternate;
    /* Nome da animação, duração, tipo de easing, repetição infinita com alternância */
}

/* Definição da Animação de Balanço Horizontal */
@keyframes horizontalSwing {
    0% {
        transform: translateX(0); /* Inicia na posição original (20px da esquerda) */
    }
    100% {
        transform: translateX(20px); /* Move 20px para a direita (ajuste este valor para a amplitude do balanço) */
    }
}

/* Garante que a imagem da logo se ajuste ao seu container */
.logo-cemeac-footer img {
    width: 100%;
    height: auto;
    display: block; 
    filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.5)); /* Sombra suave para destacar no fundo preto */
}

/* ----------- FIM DOS ESTILOS DO RODAPÉ E LOGO ----------- */
</style>
</head>

<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand">
                <i class="fas fa-video me-2"></i>Gestão de Estúdios - CEMEAC
            </a>

            <div class="nav-buttons">
                <?php if (!isset($_SESSION['usuario_id'])) : ?>
                    <a href="<?php echo $BASE_URL; ?>auth/login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                <?php else : ?>
                    <a href="<?php echo $BASE_URL; ?>pages/painel-atualizacao.php" class="btn btn-success me-2">
                        <i class="fas fa-tachometer-alt me-1"></i>Painel Administrativo
                    </a>
                    <a href="<?php echo $BASE_URL; ?>auth/logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-1"></i>Sair
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="text-center">
            <h1 class="display-board-title">
                <i class="fas fa-broadcast-tower me-2"></i>Sistema de Gerenciamento de Estúdios
            </h1>
            <p class="display-board-subtitle">Controle e acompanhamento em tempo real das gravações</p>

            <div class="airport-clock" id="airport-clock">
                <span id="current-date"></span>
                <span id="current-time"></span>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4 mb-4">
        <div class="card agendamentos-card">
            <div class="card-header d-flex align-items-center justify-content-start" style="background-color: #FFA500; color: #fff;">
                <h4 class="my-0 me-3">Professores de Hoje:</h4>
                <div id="professores-do-dia-scroller" class="professores-scroller">
                    <span class="professores-list">Carregando professores...</span>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid py-4">
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
                </tr>
            </thead>
            <tbody id="agendamentos-tbody">
                <tr>
                    <td colspan="7" class="text-center">Carregando agendamentos...</td>
                </tr>
            </tbody>
        </table>
    </div>
     <footer class="footer mt-auto py-3 bg-dark">
    <div class="logo-cemeac-footer">
        <img src="<?php echo $BASE_URL; ?>assets/img/cmeac_logo.png" alt="Logo CEMEAC">
    </div>
</footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo $BASE_URL; ?>assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Definição da URL base para uso nos scripts
        const BASE_URL = '<?php echo $BASE_URL; ?>'; // Mantendo $BASE_URL conforme seu url.php

        // Função para carregar professores do dia
        function carregarProfessoresDoDia() {
            $.ajax({
                url: BASE_URL + 'api/obter_professores_do_dia.php', // Endpoint para buscar professores
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.professores.length > 0) {
                        let professoresHtml = response.professores.map(p => `<span class="professor-name">${p}</span>`).join('<span class="separator"> • </span>');
                        const professoresListElement = document.querySelector('.professores-list');
                        if (professoresListElement) {
                            professoresListElement.innerHTML = professoresHtml;
                            iniciarScrollerProfessores(); // Iniciar o scroller após carregar os professores
                        }
                    } else {
                        const professoresListElement = document.querySelector('.professores-list');
                        if (professoresListElement) {
                            professoresListElement.textContent = 'Nenhum professor agendado para hoje.';
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar professores:', error);
                    const professoresListElement = document.querySelector('.professores-list');
                    if (professoresListElement) {
                        professoresListElement.textContent = 'Erro ao carregar professores.';
                    }
                }
            });
        }

        // Função para iniciar o efeito de scroller dos professores
        function iniciarScrollerProfessores() {
            const scroller = document.getElementById('professores-do-dia-scroller');
            const list = scroller ? scroller.querySelector('.professores-list') : null;

            if (!scroller || !list || list.scrollWidth <= scroller.clientWidth) {
                // Não inicia o scroller se o conteúdo não exceder o container
                return;
            }

            // Clona o conteúdo para criar um efeito de loop contínuo
            const clonedList = list.cloneNode(true);
            scroller.appendChild(clonedList);

            let scrollSpeed = 0.5; // Velocidade de rolagem em pixels por frame
            let currentScroll = 0;
            let animationFrameId;

            function animateScroll() {
                currentScroll += scrollSpeed;
                // Reinicia quando o primeiro conjunto de nomes sai da tela (aproximadamente)
                if (currentScroll >= list.offsetWidth) {
                    currentScroll = 0;
                }
                scroller.scrollLeft = currentScroll;
                animationFrameId = requestAnimationFrame(animateScroll);
            }

            // Para qualquer animação anterior e inicia a nova
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
            }
            animateScroll();
        }


        // Função para atualizar a tabela de agendamentos
        function atualizarTabela() {
            $.ajax({
                url: BASE_URL + 'api/obter_agendamentos.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.html) {
                        let modifiedHtml = response.html
                            // Substituição para "concluido"
                            .replace(/class="text-center text-success">concluido/g,
                                'class="badge rounded-pill bg-success text-white" style="display: inline-block; margin: 0 auto;">Gravação Concluída'
                            )
                            // Outros status
                            .replace(/class="text-center text-primary">No Horário/g,
                                'class="badge rounded-pill bg-success text-white" style="display: inline-block; margin: 0 auto;">No Horário'
                            )
                            .replace(/class="text-center text-info">Gravando/g,
                                'class="badge rounded-pill bg-info text-white" style="display: inline-block; margin: 0 auto;">Gravando'
                            )
                            .replace(/class="text-center text-warning">Atrasado/g,
                                'class="badge rounded-pill bg-warning text-dark" style="display: inline-block; margin: 0 auto;">Atrasado'
                            )
                            .replace(/class="text-center text-danger">Cancelado/g,
                                'class="badge rounded-pill bg-danger text-white" style="display: inline-block; margin: 0 auto;">Cancelado'
                            )
                            // Adiciona estilo de background branco para todas as células
                            .replace(/<tr>/g, '<tr style="background-color: white;">')
                            .replace(/<td>/g, '<td style="background-color: white;">')

                        $('#agendamentos-tbody').html(modifiedHtml);
                    } else if (response.agendamentos) { // Lógica alternativa caso a API retorne um array de agendamentos em vez de HTML
                        html = response.agendamentos.map(agendamento => {
                            const status = agendamento.status.toLowerCase();

                            let statusClass = '';
                            let statusText = agendamento.status;
                            switch (status) {
                                case 'no-horario':
                                    statusClass = 'badge rounded-pill bg-success text-white';
                                    statusText = 'No Horário';
                                    break;
                                case 'gravando':
                                    statusClass = 'badge rounded-pill bg-info text-white';
                                    statusText = 'Gravando';
                                    break;
                                case 'atrasado':
                                    statusClass = 'badge rounded-pill bg-warning text-dark';
                                    statusText = 'Atrasado';
                                    break;
                                case 'cancelado':
                                    statusClass = 'badge rounded-pill bg-danger text-white';
                                    statusText = 'Cancelado';
                                    break;
                                case 'concluido':
                                    statusClass = 'badge rounded-pill bg-success text-white';
                                    statusText = 'Gravação Concluída';
                                    break;
                                default:
                                    statusClass = 'badge rounded-pill bg-secondary text-white';
                                    statusText = agendamento.status;
                            }

                            return `
                                <tr style="background-color: white;">
                                    <td style="background-color: white;">${agendamento.professor}</td>
                                    <td style="background-color: white;">${agendamento.componente_curricular}</td>
                                    <td style="background-color: white;">${agendamento.objeto_conhecimento}</td>
                                    <td style="background-color: white;">${agendamento.estudio_id}</td>
                                    <td style="background-color: white;">${agendamento.data}</td>
                                    <td style="background-color: white;">${agendamento.hora_inicio} - ${agendamento.hora_fim}</td>
                                    <td style="background-color: white; text-align: center;">
                                        <span class="${statusClass}" style="display: inline-block; margin: 0 auto;">
                                            ${statusText}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        }).join('');

                        $('#agendamentos-tbody').html(html);
                    } else {
                        $('#agendamentos-tbody').html(
                            '<tr><td colspan="7" class="text-center">Nenhum agendamento encontrado.</td></tr>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao obter agendamentos:', error);
                    $('#agendamentos-tbody').html(
                        '<tr><td colspan="7" class="text-center text-danger">Erro ao carregar agendamentos.</td></tr>'
                    );
                }
            });
        }

        // Função para exibir a data e hora em tempo real (relógio estilo aeroporto)
        function updateClock() {
            const now = new Date();

            // Formata a data (ex: segunda-feira, 21 de julho de 2025)
            const optionsDate = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const formattedDate = now.toLocaleDateString('pt-BR', optionsDate);

            // Formata a hora (ex: 07:50:07)
            const optionsTime = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const formattedTime = now.toLocaleTimeString('pt-BR', optionsTime);

            // Atualiza os elementos HTML com a data e hora
            document.getElementById('current-date').textContent = formattedDate;
            document.getElementById('current-time').textContent = formattedTime;
        }

        // Inicialização dos scripts quando o documento estiver pronto
        $(document).ready(function() {
            // Inicializa o relógio e define a atualização a cada segundo
            updateClock();
            setInterval(updateClock, 1000);

            // Inicializa a atualização da tabela de agendamentos e define a atualização a cada 15 segundos
            atualizarTabela();
            setInterval(atualizarTabela, 15 * 1000);

            // Carrega os professores do dia e inicia o scroller
            carregarProfessoresDoDia();
        });
    </script>

</body>

</html>