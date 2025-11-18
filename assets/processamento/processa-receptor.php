<?php
session_start(); // precisa iniciar a sessão para pegar o id do funcionário
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "bancodeleite";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Recebendo dados
$nome_bebe = $conn->real_escape_string($_POST['nomebebe']);
$sexo_bebe = $conn->real_escape_string($_POST['sexobebe']);
$cpf_bebe = $conn->real_escape_string($_POST['cpfbebe']);
$data_nascimento_bebe = $_POST['data_nascimentobebe'];
$unidade_saude = $conn->real_escape_string($_POST['unidsaude']);
$situacao_clinica = $conn->real_escape_string($_POST['sitclinica']);
$observacoes_bebe = $conn->real_escape_string($_POST['observacoesbebe']);

$nome_resp = $conn->real_escape_string($_POST['nomerespbebe']);
$sexo_resp = $conn->real_escape_string($_POST['sexoresp']);
$pronomes_resp = $conn->real_escape_string($_POST['pronomesresp']);
$cpf_resp = $conn->real_escape_string($_POST['cpfrespbebe']);
$data_nascimento_resp = $_POST['data_nascimentorespbebe'];
$telefone_resp = $conn->real_escape_string($_POST['telrespbebe']);
$cep_resp = $conn->real_escape_string($_POST['ceprespbebe']);
$endereco_resp = $conn->real_escape_string($_POST['endrespbebe']);
$bairro_resp = $conn->real_escape_string($_POST['bairrorespbebe']);
$numero_resp = $conn->real_escape_string($_POST['numrespbebe']);

// Pega o id do funcionário logado
$id_funcionario = $_SESSION['id'] ?? null; // certifica-se que está logado

if(!$id_funcionario){
    echo 'erro'; // sem funcionário logado
    exit;
}

// Verifica duplicidade CPF bebê
$stmt = $conn->prepare("SELECT id FROM bebes WHERE cpf_bebe = ?");
$stmt->bind_param("s", $cpf_bebe);
$stmt->execute();
$stmt->store_result();
$cpf_bebe_existe = $stmt->num_rows > 0;
$stmt->close();

// Verifica duplicidade CPF responsável
$stmt = $conn->prepare("SELECT id FROM bebes WHERE cpf_responsavel = ?");
$stmt->bind_param("s", $cpf_resp);
$stmt->execute();
$stmt->store_result();
$cpf_resp_existe = $stmt->num_rows > 0;
$stmt->close();

// Se duplicado, retorna 'duplicado'
if($cpf_bebe_existe || $cpf_resp_existe){
    echo 'duplicado';
    $conn->close();
    exit;
}

// Inserção no banco com id_funcionario
$sql = "INSERT INTO bebes (
    nome_bebe, sexo_bebe, cpf_bebe, data_nascimento_bebe, unidade_saude, situacao_clinica, observacoes_bebe,
    nome_responsavel, sexo_responsavel, pronomes_responsavel, cpf_responsavel, data_nascimento_responsavel,
    telefone_responsavel, cep_responsavel, endereco_responsavel, numero_responsavel, bairro_responsavel,
    id_funcionario
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssssssssssi",
    $nome_bebe, $sexo_bebe, $cpf_bebe, $data_nascimento_bebe, $unidade_saude, $situacao_clinica, $observacoes_bebe,
    $nome_resp, $sexo_resp, $pronomes_resp, $cpf_resp, $data_nascimento_resp,
    $telefone_resp, $cep_resp, $endereco_resp, $numero_resp, $bairro_resp,
    $id_funcionario
);

if($stmt->execute()){
    echo 'ok'; // cadastro realizado com sucesso
} else {
    echo 'erro'; // erro inesperado
}

$stmt->close();
$conn->close();
?>
