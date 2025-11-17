<?php
include(__DIR__ . '/conexao.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// Captura os dados do formulário
$doadora_id = isset($_POST['doadora']) ? (int)$_POST['doadora'] : 0;
$data_doacao = $_POST['datadoacao'] ?? '';
$quantidade_ml = str_replace(',', '.', $_POST['qtdleite'] ?? '');
$tipo_leite = $_POST['tipo_leite'] ?? '';

// Validação básica
if (!$doadora_id || !$data_doacao || !$quantidade_ml || !$tipo_leite) {
    die("❌ Campos obrigatórios ausentes.");
}

// Inserção no banco
$sql = "INSERT INTO doacoes (doadora_id, data_doacao, quantidade_ml, tipo_leite)
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isds", $doadora_id, $data_doacao, $quantidade_ml, $tipo_leite);

if ($stmt->execute()) {
    // Redireciona para a página de cadastro com popup de sucesso
    header("Location: ../cadastro-doacao.php?msg=cadastrado");
    exit;
} else {
    die("❌ Erro ao registrar doação: " . $stmt->error);
}

$stmt->close();
$conn->close();
