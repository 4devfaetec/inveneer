<?php
include "conexao.php";
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login_form.html");
    exit;
}

// Verificando se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtendo os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $cargo = strtoupper($_POST['cargo']); // Convertendo o cargo para maiúsculas
    $tipo = 'FUNCIONARIO'; // Tipo será sempre 'FUNCIONARIO' em maiúsculas

    // Verificando se o cargo é válido
    if ($cargo === 'SUPERVISOR' || $cargo === 'VENDEDOR') {
        // Inserção no banco de dados
        $sql = "INSERT INTO cadastro_usuario (nome, email, senha, tipo, cargo) VALUES ('$nome', '$email', '$senha', '$tipo', '$cargo')";

        if ($conn->query($sql) === TRUE) {
            echo "Funcionário cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar funcionário: " . $conn->error;
        }
    } else {
        echo "Cargo inválido. Escolha entre 'SUPERVISOR' ou 'VENDEDOR'.";
    }
}
?>