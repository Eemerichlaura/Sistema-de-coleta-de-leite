<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_funcionario'])) {
    header('Location: ../index.html');
    exit;
}

if ($_SESSION['nivel_funcionario'] !== 'funcionario' &&
    $_SESSION['nivel_funcionario'] !== 'admin') {
    echo "Acesso negado.";
    exit;
}
?>
