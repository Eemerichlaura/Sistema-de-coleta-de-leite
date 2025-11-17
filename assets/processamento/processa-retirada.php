<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "bancodeleite";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$bebe_id = (int) $_POST['nomebebe'];
$data_retirada = $_POST['dataretirada'];
$quantidade_ml = str_replace(',', '.', $_POST['qtdleite']);
$tipo_leite = $_POST['tipo_leite'];

if (!$bebe_id || !$data_retirada || !$quantidade_ml || !$tipo_leite) {
    die("❌ Campos obrigatórios ausentes.");
}

$sql = "INSERT INTO retiradas (bebe_id, data_retirada, quantidade_ml, tipo_leite)
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isds", $bebe_id, $data_retirada, $quantidade_ml, $tipo_leite);

if ($stmt->execute()) {
    // Redireciona para página de cadastro com parâmetro de sucesso
    header("Location: ../cadastro-retirada.php?msg=cadastrado");
    exit;
} else {
    die("❌ Erro ao registrar retirada: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
