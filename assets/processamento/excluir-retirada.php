<?php
include(__DIR__ . '/conexao.php');

// Verifica se a requisição veio via POST
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

$id = intval($_POST['id']); // garante que seja um número

// Prepara e executa a exclusão
$stmt = $conn->prepare("DELETE FROM retiradas WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo "Erro na preparação da query: " . $conn->error;
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "ok"; // sucesso
} else {
    http_response_code(500);
    echo "Erro ao excluir: " . $stmt->error;
}

$stmt->close();
$conn->close();
