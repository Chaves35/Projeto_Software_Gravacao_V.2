/**
 * CEMEAC Estúdios - Script principal
 * Baseado no conceito de painéis de aeroporto
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('CEMEAC Estúdios - Sistema de Gerenciamento iniciado');

    // Inicializar componentes
    initClock();
    initCarousel();
    setupButtons();

    // Auto atualização da página a cada 5 minutos
    setInterval(checkForUpdates, 5 * 60 * 1000); // 5 minutos
    
    // Inicialização do banner de professores
    initProfessoresBanner();
});

/**
 * Inicializa o relógio estilo aeroporto
 */
function initClock() {
    updateClock();
    setInterval(updateClock, 1000);
}

/**
 * Atualiza o relógio com data e hora atual
 */
function updateClock() {
    const now = new Date();

    // Formatação da data: DD/MM/YYYY
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    const dateStr = `${day}/${month}/${year}`;

    // Formatação da hora: HH:MM:SS
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const timeStr = `${hours}:${minutes}:${seconds}`;

    // Atualizando elementos no DOM
    const currentDateElement = document.getElementById('current-date');
    const currentTimeElement = document.getElementById('current-time');

    if (currentDateElement && currentTimeElement) { // Verifica se os elementos existem
        currentDateElement.textContent = dateStr;
        currentTimeElement.textContent = timeStr;
    }
}

/**
 * Inicializa o carrossel de estúdios
 */
function initCarousel() {
    try {
        const carousel = $('.studio-carousel');  // Seleciona o carrossel

        if (carousel.length) {
            carousel.owlCarousel({
                loop: true,
                margin: 20,
                nav: true,  // Ativa a navegação
                dots: true,
                autoplay: true,
                autoplayTimeout: 10000, // 10 segundos entre slides
                autoplayHoverPause: true,
                responsive: {
                    0: { items: 1 },
                    600: { items: 1 },
                    1000: { items: 1 }
                }
            });

            console.log("Carrossel inicializado com sucesso");
        } else {
            console.warn("Elemento do carrossel não encontrado");
        }
    } catch (error) {
        console.error("Erro ao inicializar carrossel:", error);
    }
}

/**
 * Configura botões de navegação do carrossel
 */
function setupButtons() {
    // Botões de navegação do carrossel
    $('.carousel-prev').on('click', function() {
        if (window.studioCarousel) {
            window.studioCarousel.trigger('prev.owl.carousel');
        }
    });

    $('.carousel-next').on('click', function() {
        if (window.studioCarousel) {
            window.studioCarousel.trigger('next.owl.carousel');
        }
    });
}

/**
 * Efeito visual para destacar um agendamento recém atualizado
 * @param {number} agendamentoId - ID do agendamento a destacar
 */
function highlightAgendamento(agendamentoId) {
    const row = document.querySelector(`tr[data-id="${agendamentoId}"]`);
    if (row) {
        row.classList.add('highlight-row');
        setTimeout(() => {
            row.classList.remove('highlight-row');
        }, 2000);
    }
}

/**
 * Verificar e exibir agendamentos com status recém alterados
 * Função para ser chamada via AJAX se implementarmos atualizações em tempo real
 */
function checkForUpdates() {
    // Implementar com AJAX se necessário para atualizar em tempo real
    // Exemplo: fetch('/api/recent-updates');
}

/**
 * Evento de clicar no botão de atualizar status
 */
$(document).on('click', '[data-bs-toggle="modal"][data-bs-target="#statusModal"]', function() {
    const agendamentoId = $(this).data('id');
    const status = $(this).data('status');

    // Definindo os valores no modal
    $('#agendamentoId').val(agendamentoId);
    $('#statusSelect').val(status);
});

/**
 * Manipulação do envio do formulário de atualização de status
 */
$('#updateStatusForm').on('submit', function(event) {
    event.preventDefault(); // Previne o comportamento padrão do formulário

    const formData = $(this).serialize(); // Obtém todos os dados do formulário

    $.ajax({
        type: 'POST',
        url: BASE_URL + 'api/atualizar_status.php', // Usando a variável BASE_URL
        data: formData,
        success: function(response) {
            // Aqui você pode manipular a resposta
            if (response.success) {
                highlightAgendamento(response.agendamento_id); // Usando sua função de destaque
                $('#statusModal').modal('hide'); // Fecha o modal
            } else {
                alert('Erro ao atualizar status: ' + response.message);
            }
        },
        error: function() {
            alert('Erro na comunicação com o servidor.');
        }
    });
});

/**
 * Inicializa o banner de professores
 */
function initProfessoresBanner() {
    // Verifica se o elemento existe antes de carregar
    if (document.querySelector('.professor-names')) {
        // Carregar professores inicialmente
        carregarProfessoresDoDia();

        // Atualizar a cada 1 minuto
        setInterval(carregarProfessoresDoDia, 60 * 1000);
    }
}

/**
 * Função para carregar professores do dia
 * Parte do Sistema de Gerenciamento de Estúdios CEMEAC
 */
function carregarProfessoresDoDia() {
    $.ajax({
        url: BASE_URL + 'api/obter_professores.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            let html = '<div class="professor-credits">';
            html += '<div class="professor-name professor-title">PROFESSORES DO DIA</div>';

            if (response.professores && response.professores.length > 0) {
                // Adiciona nomes duplicados para efeito infinito
                response.professores.forEach(function(prof) {
                    html += `<div class="professor-name">${prof.nome}</div>`;
                });                
            } else {
                html += '<div class="professor-name">Nenhuma gravação hoje</div>';
            }

            html += '</div>';
            $('.professor-names').html(html);

            // Exibe o banner
            $('.professor-banner-container').removeClass('hiding').css('opacity', 1);

            // Fecha após 30s ou 5s dependendo do conteúdo
            const delay = (response.professores && response.professores.length > 0) ? 30000 : 5000;
            setTimeout(() => {
                $('.professor-banner-container').addClass('hiding');
            }, delay);
        },
        error: function() {
            $('.professor-names').html('<div class="professor-credits"><div class="professor-name">Erro ao carregar professores</div></div>');
            $('.professor-banner-container').removeClass('hiding').css('opacity', 1);
            setTimeout(() => {
                $('.professor-banner-container').addClass('hiding');
            }, 5000);
        }
    });
}

/**
 * Manipulação do formulário de novo agendamento
 * Script para processar o envio do formulário de adição de agendamentos
 */
$(document).ready(function() {
    // Verifica se o formulário existe na página
    if ($('#formNovoAgendamento').length > 0) {
        console.log('Formulário de novo agendamento detectado, inicializando handler');
        
        // Manipula o evento de submissão do formulário
        $('#formNovoAgendamento').on('submit', function(event) {
            event.preventDefault();
            console.log('Formulário de novo agendamento submetido');
            
            // Serializa os dados do formulário para envio
            const formData = $(this).serialize();
            console.log('Dados do formulário:', formData);
            
            // Envia os dados via AJAX
            $.ajax({
                url: BASE_URL + 'api/adicionar_agendamento.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta do servidor:', response);
                    
                    // Processa a resposta do servidor
                    if (response && response.success) {
                        // Limpa o formulário
                        $('#formNovoAgendamento')[0].reset();
                        
                        // Fecha o modal
                        $('#adicionarAgendamentoModal').modal('hide');
                        
                        // Exibe mensagem de sucesso
                        alert('Agendamento cadastrado com sucesso!');
                        
                        // Recarrega a página para mostrar o novo agendamento
                        window.location.reload();
                    } else {
                        // Exibe mensagem de erro
                        alert('Erro: ' + (response && response.message ? response.message : 'Falha ao cadastrar agendamento'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição AJAX:', xhr.responseText);
                    alert('Erro de comunicação com o servidor. Verifique o console para mais detalhes.');
                }
            });
        });
    }
});