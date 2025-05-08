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

        // Verificar se o tipo de usuário é válido
        if (!in_array($_SESSION['tipo'], ['ADMIN', 'FUNCIONARIO'])) {
            echo "Tipo de usuário inválido.";
            exit;
        }

        // Redirecionamento conforme o tipo de usuário
        if ($_SESSION['tipo'] === 'ADMIN') {
            header("Location: ../app/admin/main.html");
        } elseif ($_SESSION['tipo'] === 'FUNCIONARIO') {
            header("Location: painel_funcionario.php");
        }
        exit;
    } else {
        echo "Login inválido."; // Mensagem genérica
    }
} else {
    echo "Login inválido."; // Mensagem genérica
}
?>

