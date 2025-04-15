<?php
include "conexao.php";
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login_form.html");
    exit;
}

// Simulação – depois você troca por $_POST
$nome = "Carlos Silva";
$email = "carlos@empresa.com";
$senha = password_hash("carlos123", PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES ('$nome', '$email', '$senha', 'funcionario')";

if ($conn->query($sql) === TRUE) {
    echo "Funcionário cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar funcionário: " . $conn->error;
}
?>