<?php
include "conexao.php";
session_start();

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM cadastro_usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = strtoupper($usuario['tipo']); // CAPS LOCK

        if ($_SESSION['tipo'] === 'ADMIN') {
            header("Location: painel_admin.php");
        } elseif ($_SESSION['tipo'] === 'FUNCIONARIO') {
            header("Location: painel_funcionario.php");
        } else {
            echo "Tipo de usuário inválido.";
        }
        exit;
    } else {
        echo "Senha incorreta.";
    }
} else {
    echo "Usuário não encontrado.";
}
?>
