<?php
include "conexao.php";

$nome = "admin";
$email = "admin@empresa.com";
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); 
$tipo = $_POST['tipo']; 

$sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES ('$nome', '$email', '$senha', 'admin')";

if ($conn->query($sql) === TRUE) {
    echo "Administrador criado com sucesso!";
} else {
    echo "Erro ao criar admin: " . $conn->error;
}

$conn->close();
?>