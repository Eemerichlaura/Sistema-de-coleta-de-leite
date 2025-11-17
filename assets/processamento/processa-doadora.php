<?php
include(__DIR__ . '/conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

    if($result->num_rows > 0){
        echo "duplicado"; 
        exit;
    }

    // Inserir no banco
    $stmt = $conn->prepare("INSERT INTO doadoras (nome, cpf, data_nascimento, telefone, cep, endereco, numero, bairro, observacoes) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssss", $nome, $cpf, $data_nascimento, $telefone, $cep, $endereco, $numero, $bairro, $observacoes);

    if($stmt->execute()){
        echo "ok"; 
    } else {
        echo "erro"; 
    }

    $conn->close();
}
?>
