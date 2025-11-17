<?php
// Inicia a sessão apenas se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conexao.php'; // conexão com o banco

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.html");
    exit;
}

// Recebe os dados do formulário
$usuario = trim($_POST['usuario']);
$senha = trim($_POST['senha']);

// Validação simples
if (empty($usuario) || empty($senha)) {
    echo "<script>alert('Preencha todos os campos.'); window.location.href='../index.html';</script>";
    exit;
}

// Determina se é CPF ou email
$campo = filter_var($usuario, FILTER_VALIDATE_EMAIL) ? 'email' : 'cpf';

// Prepara a consulta segura
$sql = "SELECT * FROM usuarios WHERE $campo = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da query: " . $conn->error);
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se encontrou o usuário
if ($result->num_rows === 0) {
    echo "<script>alert('Usuário não encontrado.'); window.location.href='../index.html';</script>";
    exit;
}

// Busca os dados do usuário
$user = $result->fetch_assoc();

// Verifica a senha
if (!password_verify($senha, $user['senha'])) {
    echo "<script>alert('Senha incorreta.'); window.location.href='../index.html';</script>";
    exit;
}

// Cria a sessão para o usuário
$_SESSION['id'] = $user['id'];
$_SESSION['nome'] = $user['nome'];
$_SESSION['nivel'] = $user['nivel'];

// Redireciona para a página principal
header("Location: ../home.php");
exit;
?>
