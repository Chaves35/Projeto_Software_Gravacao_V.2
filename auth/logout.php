<?php
session_start(); // Inicie a sessão

// Verifica se está logado
if (isset($_SESSION['usuario_id'])) {
    // Remove todas as variáveis de sessão
    session_unset();
    
    // Destroi a sessão
    session_destroy();

    // Redireciona para a página de login
    header("Location: login.php");
    exit();
} else {
    // Se não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit();
}
?>