<?php
session_start();
include "conexao.php"; 
include "verifica-funcionario.php"; 

if(isset($_POST['id'])) {
    $id = intval($_POST['id']);

    if($id <= 0){
        echo "ID inválido";
        exit;
    }

    if(isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id){
        echo "Você não pode excluir a si mesmo.";
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        echo "ok"; // <- isso dispara o popup de sucesso no JS
        exit;
    } else {
        echo "Erro ao excluir: " . $stmt->error;
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ID não informado";
    exit;
}
?>
