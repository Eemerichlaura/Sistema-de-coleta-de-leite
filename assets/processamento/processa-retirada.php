<?php
session_start(); // necessário para acessar $_SESSION
include(__DIR__ . '/verifica-funcionario.php'); // garante que só funcione para funcionários logados

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "bancodeleite";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Captura os dados do formulário
$bebe_id = (int) $_POST['nomebebe'];
$data_retirada = $_POST['dataretirada'];
$quantidade_ml = str_replace(',', '.', $_POST['qtdleite']);
$tipo_leite = $_POST['tipo_leite'];

// Captura o ID do funcionário logado
$id_funcionario = $_SESSION['id'] ?? 0;

// Validação
if (!$bebe_id || !$data_retirada || !$quantidade_ml || !$tipo_leite) {
    die("❌ Campos obrigatórios ausentes.");
}

// Inserção no banco
$sql = "INSERT INTO retiradas (bebe_id, data_retirada, quantidade_ml, tipo_leite, id_funcionario)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isdsi", $bebe_id, $data_retirada, $quantidade_ml, $tipo_leite, $id_funcionario);

if ($stmt->execute()) {
    // Redireciona para página de cadastro com parâmetro de sucesso
    header("Location: ../cadastro-retirada.php?msg=cadastrado");
    exit;
} else {
    die("❌ Erro ao registrar retirada: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
