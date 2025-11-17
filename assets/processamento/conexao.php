<?php
$servername = "localhost";
$username = "root";
$password = ""; // ou sua senha
$dbname = "bancodeleite";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}
?>
