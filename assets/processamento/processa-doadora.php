<?php
// Inicia a sessão apenas se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Pega o ID do funcionário logado
    $id_funcionario = $_SESSION['id'] ?? null;

    // Segurança: não cadastra se não tiver funcionário logado
    if (!$id_funcionario) {
        echo "erro_sessao";
        exit;
    }

    // Escapa os dados recebidos do formulário
    $nome = $conn->real_escape_string($_POST['nome']);
    $cpf = $conn->real_escape_string($_POST['cpf']);
    $data_nascimento = $conn->real_escape_string($_POST['data_nascimento']);
    $telefone = $conn->real_escape_string($_POST['telefone']);
    $cep = $conn->real_escape_string($_POST['cep']);
    $endereco = $conn->real_escape_string($_POST['endereco']);
    $numero = $conn->real_escape_string($_POST['numero']);
    $bairro = $conn->real_escape_string($_POST['bairro']);
    $observacoes = $conn->real_escape_string($_POST['observacoes']);

    // Verifica CPF duplicado
    $stmt = $conn->prepare("SELECT id FROM doadoras WHERE cpf = ?");
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "duplicado"; 
        exit;
    }

    // Insere no banco com id_funcionario
    $stmt = $conn->prepare("
        INSERT INTO doadoras 
        (nome, cpf, data_nascimento, telefone, cep, endereco, numero, bairro, observacoes, id_funcionario)
        VALUES (?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "sssssssssi",
        $nome,
        $cpf,
        $data_nascimento,
        $telefone,
        $cep,
        $endereco,
        $numero,
        $bairro,
        $observacoes,
        $id_funcionario
    );

    if ($stmt->execute()) {
        echo "ok"; 
    } else {
        echo "erro"; 
    }

    $stmt->close();
    $conn->close();
}
?>
