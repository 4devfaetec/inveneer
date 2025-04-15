<?php
include "conexao.php";

$nome = "admin";
$email = "admin@empresa.com";
$senha = password_hash("admin123", PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES ('$nome', '$email', '$senha', 'admin')";

if ($conn->query($sql) === TRUE) {
    echo "Administrador criado com sucesso!";
} else {
    echo "Erro ao criar admin: " . $conn->error;
}

$conn->close();
?>