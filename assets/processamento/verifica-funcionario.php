<?php
// Inicia a sessão apenas se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

// Permitir tanto funcionário quanto admin
if ($_SESSION['nivel'] !== 'funcionario' && $_SESSION['nivel'] !== 'admin') {
    echo "Acesso negado.";
    exit;
}
?>
