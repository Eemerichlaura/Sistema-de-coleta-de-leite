<?php
include(__DIR__ . '/conexao.php');
include(__DIR__ . '/verifica-funcionario.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método não permitido";
    exit;
}

if (!isset($_POST['id'])) {
    http_response_code(400);
    echo "ID não informado";
    exit;
}

$id = intval($_POST['id']);

// Verifica se a doadora existe
$stmt = $conn->prepare("SELECT id FROM doadoras WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo "Doadora não encontrada";
    exit;
}

// Exclui a doadora
$stmt = $conn->prepare("DELETE FROM doadoras WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Retorna apenas "ok" para o JS identificar sucesso
    echo "ok";
} else {
    http_response_code(500);
    echo "Erro ao excluir: " . $stmt->error;
}

$stmt->close();
$conn->close();
