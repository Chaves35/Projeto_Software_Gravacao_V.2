<?php
// Verifica se a constante já foi definida
if (!defined('SISTEMA_INTERNO')) {
    define('SISTEMA_INTERNO', true); // Define uma constante para segurança (pode ser usada para evitar acesso direto)
}

// Ambiente de desenvolvimento ou produção
define('ENVIRONMENT', 'development'); // Mude para 'production' em produção

// Configurações e variáveis globais podem ser definidas aqui
// Você pode adicionar outras configurações relacionadas ao sistema, como logs, mensagens, etc.

// Incluir o arquivo de URL
require_once('url.php'); // Inclui o arquivo que define $BASE_URL
?>