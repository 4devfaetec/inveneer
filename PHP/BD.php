<?php
$host = "localhost";
$usuario = "root";
$senha = "sua_senha";
$banco = "Login_Usuario";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

echo "Conectado com sucesso!";
?>
