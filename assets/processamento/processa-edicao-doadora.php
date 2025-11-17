<?php
include(__DIR__ . '/conexao.php');
if (session_status() === PHP_SESSION_NONE) session_start();
include "verifica-funcionario.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "erro: Acesso inválido.";
    exit;
}

// Recebe e limpa os dados
$id = intval($_POST['id']);
$nome = trim($_POST['nome']);
$cpf = trim($_POST['cpf']);
$data_nascimento = $_POST['data_nascimento'];
$telefone = trim($_POST['telefone']);
$cep = trim($_POST['cep']);
$endereco = trim($_POST['endereco']);
$numero = trim($_POST['numero']);
$bairro = trim($_POST['bairro']);
$observacoes = trim($_POST['observacoes']);

// Valida campos obrigatórios
if (empty($nome) || empty($cpf) || empty($data_nascimento) || empty($telefone) || empty($cep) || empty($endereco) || empty($numero) || empty($bairro)) {
    echo "erro: Preencha todos os campos obrigatórios.";
    exit;
}

// Verifica se o CPF já existe em outro registro
$stmtCheck = $conn->prepare("SELECT id FROM doadoras WHERE cpf = ? AND id != ?");
$stmtCheck->bind_param("si", $cpf, $id);
$stmtCheck->execute();
$stmtCheck->store_result();
if($stmtCheck->num_rows > 0){
    echo "duplicado";
    $stmtCheck->close();
    $conn->close();
    exit;
}
$stmtCheck->close();

// Atualiza a doadora
$stmt = $conn->prepare("UPDATE doadoras SET nome = ?, cpf = ?, data_nascimento = ?, telefone = ?, cep = ?, endereco = ?, numero = ?, bairro = ?, observacoes = ? WHERE id = ?");
$stmt->bind_param("sssssssssi", $nome, $cpf, $data_nascimento, $telefone, $cep, $endereco, $numero, $bairro, $observacoes, $id);

if($stmt->execute()){
    echo "ok";
} else {
    echo "erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
