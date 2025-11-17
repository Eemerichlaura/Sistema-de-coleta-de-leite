<?php
// processa a exclusão de uma doação
include 'conexao.php';

if (session_status() === PHP_SESSION_NONE) session_start();
include "verifica-funcionario.php";

// Verifica se o ID foi enviado e é numérico
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = intval($_POST['id']); // garante que seja um número inteiro

    // Prepara a query para deletar
    $stmt = $conn->prepare("DELETE FROM doacoes WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo 'ok'; // JS entende que deu certo
    } else {
        echo 'Erro ao excluir doação';
    }

    $stmt->close();
} else {
    echo 'ID inválido';
}

$conn->close();
?>
