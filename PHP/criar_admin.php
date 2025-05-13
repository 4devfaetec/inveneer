<?php
include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); 
    $tipo  = strtoupper($_POST['tipo']); // ADMIN
    $cargo = 'GERENTE'; // Cargo fixo

    $sql = "INSERT INTO cadastro_usuario (nome, email, senha, tipo, cargo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nome, $email, $senha, $tipo, $cargo);

    if ($stmt->execute()) {
        echo "<script>alert('Administrador cadastrado com sucesso!'); window.location.href = 'login_form.html';</script>";
    } else {
        echo "<script>alert('Erro ao criar administrador: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Acesso inválido.'); window.history.back();</script>";
}
?>
