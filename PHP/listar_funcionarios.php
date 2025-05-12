<?php
include "conexao.php";
session_start();

// Verifica se está logado como admin
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login_form.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); 
    $tipo  = $_POST['tipo']; //

    $sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $senha, $tipo);

    if ($stmt->execute()) {
        echo "<script>alert('Funcionário cadastrado com sucesso!'); window.location.href = 'painel_admin.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar funcionário: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Acesso inválido.'); window.history.back();</script>";
}
?>
