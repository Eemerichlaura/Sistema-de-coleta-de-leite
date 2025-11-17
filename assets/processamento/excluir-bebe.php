<?php
include(__DIR__ . '/conexao.php');
include(__DIR__ . '/verifica-funcionario.php');

// Permite apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método não permitido";
    exit;
}

// Verifica se o ID foi enviado
if (!isset($_POST['id']) || empty($_POST['id'])) {
    http_response_code(400);
    echo "ID não informado";
    exit;
}

$id = intval($_POST['id']);

// Verifica se o bebê existe
$stmt = $conn->prepare("SELECT id FROM bebes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo "Bebê não encontrado";
    exit;
}

// Exclui o bebê
$stmt = $conn->prepare("DELETE FROM bebes WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "ok";
} else {
    http_response_code(500);
    echo "Erro ao excluir";
}

$stmt->close();
$conn->close();
