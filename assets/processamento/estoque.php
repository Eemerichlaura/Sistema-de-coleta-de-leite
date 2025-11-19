<?php
require_once 'conexao.php';
session_start();

// Verifica parâmetros obrigatórios
if (!isset($_POST['tipo'], $_POST['quantidade'], $_POST['acao'])) {
    header("Location: ../home.php?msg=erro");
    exit;
}

$tipo = trim($_POST['tipo']);
$quantidade = intval($_POST['quantidade']);
$acao = $_POST['acao'];
$funcionario = $_SESSION['nome_funcionario'] ?? 'Desconhecido';

// Evita valores inválidos
if ($quantidade <= 0 || !in_array($acao, ['entrada', 'saida'])) {
    header("Location: ../home.php?msg=erro");
    exit;
}

// Verifica se o tipo existe
$stmt = $conn->prepare("SELECT id, quantidade FROM estoqueleite WHERE tipo = ?");
$stmt->bind_param("s", $tipo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: ../home.php?msg=erro");
    exit;
}

$dados = $resultado->fetch_assoc();
$novo_total = $dados['quantidade'];

// Calcula novo estoque
if ($acao === 'entrada') {
    $novo_total += $quantidade;
} else {
    // Saída — previne estoque negativo
    $novo_total = max(0, $novo_total - $quantidade);
}

// Atualiza o estoque
$update = $conn->prepare("UPDATE estoqueleite SET quantidade = ? WHERE id = ?");
$update->bind_param("ii", $novo_total, $dados['id']);
$update->execute();

// Registra log
$log = $conn->prepare("
    INSERT INTO logleite (tipo, quantidade, acao, funcionario, data) 
    VALUES (?, ?, ?, ?, NOW())
");
$log->bind_param("siss", $tipo, $quantidade, $acao, $funcionario);
$log->execute();

// Redireciona com sucesso
header("Location: ../home.php?msg=atualizado");
exit;
?>
