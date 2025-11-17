<?php
session_start();
include 'conexao.php'; // ajuste o caminho conforme seu projeto

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);
    $senha_raw = $_POST['senha'];
    $nivel = $_POST['nivel'];

    // Validação básica
    if (empty($nome) || empty($cpf) || empty($email) || empty($senha_raw) || empty($nivel)) {
        // Redireciona de volta com erro (poderia usar ?erro=1)
        header('Location: ../cadastrar-funcionario.php');
        exit;
    }

    // Valida o nível
    if ($nivel !== 'admin' && $nivel !== 'funcionario') {
        $nivel = 'funcionario'; // padrão
    }

    // Criptografa a senha
    $senha = password_hash($senha_raw, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, cpf, email, senha, nivel) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("sssss", $nome, $cpf, $email, $senha, $nivel);

    if ($stmt->execute()) {
        // Redireciona para o formulário com sucesso=1
        header('Location: ../cadastro-funcionarios.php?sucesso=1');
        exit;
    } else {
        die("Erro ao cadastrar: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("Método inválido.");
}
?>
