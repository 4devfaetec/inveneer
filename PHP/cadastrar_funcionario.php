<?php
include "conexao.php";
session_start();

// Verifica se o usuário está logado e se é admin
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login_form.html");
    exit;
}

// Verifica se os dados vieram por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo  = 'funcionario'; // fixo aqui, ou pode pegar de $_POST se quiser flexível

    $sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES ('$nome', '$email', '$senha', '$tipo')";

    if ($conn->query($sql) === TRUE) {
        echo "Funcionário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar funcionário: " . $conn->error;
    }
} else {
    echo "Requisição inválida.";
}
?>
