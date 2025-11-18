<?php
include(__DIR__ . '/conexao.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// Captura os dados do formulário
$doadora_id = isset($_POST['doadora']) ? (int)$_POST['doadora'] : 0;
$data_doacao = $_POST['datadoacao'] ?? '';
$quantidade_ml = str_replace(',', '.', $_POST['qtdleite'] ?? '');
$tipo_leite = $_POST['tipo_leite'] ?? '';
$id_funcionario = $_SESSION['id'] ?? 0; // ID do funcionário logado

// Validação básica
if (!$doadora_id || !$data_doacao || !$quantidade_ml || !$tipo_leite || !$id_funcionario) {
    die("❌ Campos obrigatórios ausentes.");
}

// Inserção no banco com funcionário
$sql = "INSERT INTO doacoes (doadora_id, data_doacao, quantidade_ml, tipo_leite, id_funcionario)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isdsi", $doadora_id, $data_doacao, $quantidade_ml, $tipo_leite, $id_funcionario);

if ($stmt->execute()) {
    // Redireciona para a página de cadastro com popup de sucesso
    header("Location: ../cadastro-doacao.php?msg=cadastrado");
    exit;
} else {
    die("❌ Erro ao registrar doação: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
