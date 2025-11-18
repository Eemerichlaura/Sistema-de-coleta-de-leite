<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.html");
    exit;
}

$usuario = trim($_POST['usuario']);
$senha   = trim($_POST['senha']);

if (empty($usuario) || empty($senha)) {
    echo "<script>alert('Preencha todos os campos.'); window.location.href='../index.html';</script>";
    exit;
}

// Verifica se é CPF ou email
if (filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
    $sql = "SELECT * FROM usuarios WHERE email = ?";
} else {
    $usuario = preg_replace('/[^0-9]/', '', $usuario);
    $sql = "SELECT * FROM usuarios WHERE REPLACE(REPLACE(cpf,'.',''),'-','') = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Usuário não encontrado.'); window.location.href='../index.html';</script>";
    exit;
}

$usuarioEncontrado = $result->fetch_assoc();

// Verificação correta da senha
if (!password_verify($senha, $usuarioEncontrado['senha'])) {
    echo "<script>alert('Senha incorreta.'); window.location.href='../index.html';</script>";
    exit;
}

// Sessão
$_SESSION['id_funcionario'] = $usuarioEncontrado['id'];
$_SESSION['nome_funcionario'] = $usuarioEncontrado['nome'];
$_SESSION['nivel_funcionario'] = $usuarioEncontrado['nivel'];

header("Location: ../home.php");
exit;
?>
