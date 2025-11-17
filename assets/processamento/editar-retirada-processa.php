<?php
include "conexao.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $bebe_id = intval($_POST['nomebebe']);
    $data = $_POST['dataretirada'];
    $tipo_leite = $_POST['tipo_leite'];
    $quantidade = intval($_POST['qtdleite']);

    $stmt = $conn->prepare("UPDATE retiradas SET bebe_id=?, data_retirada=?, tipo_leite=?, quantidade_ml=? WHERE id=?");
    $stmt->bind_param("issii", $bebe_id, $data, $tipo_leite, $quantidade, $id);

    if ($stmt->execute()) {
        header("Location: ../editar-retirada.php?id=$id&msg=editado");
        exit;
    } else {
        die("Erro ao atualizar: " . $conn->error);
    }
}
?>
