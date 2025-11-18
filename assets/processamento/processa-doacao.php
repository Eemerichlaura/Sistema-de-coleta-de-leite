<?php
session_start(); // necessário para acessar $_SESSION
include(__DIR__ . '/verifica-funcionario.php'); // garante que só funcione para funcionários logados

$host = "localhost";
$user = "root"; 
$pass = "";
$dbname = "bancodeleite";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

// Captura os dados do formulário
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$doadora_id = (int) $_POST['doadora'];
$data_doacao = $_POST['datadoacao'];
$quantidade_ml = str_replace(',', '.', $_POST['qtdleite']);
$tipo_leite = $_POST['tipo_leite'];

// Captura o ID do funcionário logado
$id_funcionario = $_SESSION['id'] ?? 0;

// Validação
if (!$doadora_id || !$data_doacao || !$quantidade_ml || !$tipo_leite) {
    die("❌ Campos obrigatórios ausentes.");
}

if($id > 0){
    // === EDIÇÃO ===
    $sql = "UPDATE doacoes 
            SET doadora_id=?, data_doacao=?, quantidade_ml=?, tipo_leite=?, id_funcionario=?
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdsii", $doadora_id, $data_doacao, $quantidade_ml, $tipo_leite, $id_funcionario, $id);
    if($stmt->execute()){
        header("Location: ../editar-doacao.php?id=$id&msg=editado");
        exit;
    } else {
        die("❌ Erro ao atualizar: " . $stmt->error);
    }
} else {
    // === INSERÇÃO ===
    $sql = "INSERT INTO doacoes (doadora_id, data_doacao, quantidade_ml, tipo_leite, id_funcionario) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdsd", $doadora_id, $data_doacao, $quantidade_ml, $tipo_leite, $id_funcionario);
    if($stmt->execute()){
        header("Location: ../cadastrar-doacao.php?msg=cadastrado");
        exit;
    } else {
        die("❌ Erro ao registrar doação: " . $stmt->error);
    }
}

$stmt->close();
$conn->close();
?>
