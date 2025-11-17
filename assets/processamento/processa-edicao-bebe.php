<?php
include(__DIR__ . '/conexao.php');
include "verifica-funcionario.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// Verifica se todos os campos obrigatórios foram enviados
if (!isset($_POST['id'])) {
    die("ID do bebê não informado.");
}

$id = intval($_POST['id']);

// Dados do bebê
$nome_bebe = $_POST['nomebebe'] ?? '';
$sexo_bebe = $_POST['sexobebe'] ?? '';
$cpf_bebe = $_POST['cpfbebe'] ?? '';
$data_nascimento_bebe = $_POST['data_nascimentobebe'] ?? '';
$unidade_saude = $_POST['unidsaude'] ?? '';
$situacao_clinica = $_POST['sitclinica'] ?? '';
$observacoes_bebe = $_POST['observacoesbebe'] ?? '';

// Dados do responsável
$nome_responsavel = $_POST['nomerespbebe'] ?? '';
$sexo_responsavel = $_POST['sexoresp'] ?? '';
$pronomes_responsavel = $_POST['pronomesresp'] ?? '';
$cpf_responsavel = $_POST['cpfrespbebe'] ?? '';
$data_nascimento_responsavel = $_POST['data_nascimentorespbebe'] ?? '';
$telefone_responsavel = $_POST['telrespbebe'] ?? '';
$cep_responsavel = $_POST['ceprespbebe'] ?? '';
$endereco_responsavel = $_POST['endrespbebe'] ?? '';
$bairro_responsavel = $_POST['bairrorespbebe'] ?? '';
$numero_responsavel = $_POST['numrespbebe'] ?? '';

// Validações básicas (ex.: campos obrigatórios)
if (empty($nome_bebe) || empty($cpf_bebe) || empty($nome_responsavel) || empty($cpf_responsavel)) {
    die("Campos obrigatórios não foram preenchidos.");
}

// Prepara query de update
$sql = "UPDATE bebes SET
        nome_bebe = ?, sexo_bebe = ?, cpf_bebe = ?, data_nascimento_bebe = ?, unidade_saude = ?, situacao_clinica = ?, observacoes_bebe = ?,
        nome_responsavel = ?, sexo_responsavel = ?, pronomes_responsavel = ?, cpf_responsavel = ?, data_nascimento_responsavel = ?, telefone_responsavel = ?, cep_responsavel = ?, endereco_responsavel = ?, bairro_responsavel = ?, numero_responsavel = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssssssssssssssi",
    $nome_bebe, $sexo_bebe, $cpf_bebe, $data_nascimento_bebe, $unidade_saude, $situacao_clinica, $observacoes_bebe,
    $nome_responsavel, $sexo_responsavel, $pronomes_responsavel, $cpf_responsavel, $data_nascimento_responsavel, $telefone_responsavel, $cep_responsavel, $endereco_responsavel, $bairro_responsavel, $numero_responsavel,
    $id
);


if ($stmt->execute()) {
    // Redireciona para a página de edição com mensagem de sucesso
    header("Location: ../editar-bebe.php?id=$id&msg=editado");
    exit;
} else {
    die("Erro ao atualizar: " . $stmt->error);
}
