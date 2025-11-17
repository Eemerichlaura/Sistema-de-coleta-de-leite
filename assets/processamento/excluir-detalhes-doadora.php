<?php
include(__DIR__ . '/conexao.php');
include(__DIR__ . '/verifica-funcionario.php');

header('Content-Type: application/json; charset=utf-8');

// Aceita apenas método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido']);
    exit;
}

// Tenta capturar o ID tanto de $_POST['excluir_id'] quanto de $_POST['id']
$id = null;
if (isset($_POST['excluir_id'])) {
    $id = intval($_POST['excluir_id']);
} elseif (isset($_POST['id'])) {
    $id = intval($_POST['id']);
}

// Verifica se o ID é válido
if (empty($id) || $id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID inválido ou não informado']);
    exit;
}

// Confirma se o registro existe antes de excluir
$check = $conn->prepare("SELECT id FROM doadoras WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Doadora não encontrada']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Executa a exclusão
$stmt = $conn->prepare("DELETE FROM doadoras WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'mensagem' => 'Doadora excluída com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao excluir: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
exit;
