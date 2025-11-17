<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['nivel'] !== 'admin') {
    echo "Acesso negado: somente administradores.";
    exit;
}
?>
