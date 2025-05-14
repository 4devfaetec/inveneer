<?php
include "conexao.php";
session_start();

// Verifica se o usuário está logado e se é ADMIN
if (!isset($_SESSION['id']) || strtoupper($_SESSION['tipo']) !== 'ADMIN') {
    header("Location: ../index.html");
    exit;
}

// Função para definir o tipo com base no cargo
function definirTipoUsuario($cargo) {
    $cargo = strtoupper($cargo);
    if ($cargo === 'GERENTE') {
        return 'ADMIN';
    } elseif (in_array($cargo, ['SUPERVISOR', 'SUPERVISORA', 'VENDEDOR', 'VENDEDORA'])) {
        return 'FUNCIONARIO';
    } else {
        return false; // Cargo inválido
    }
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $cargo = strtoupper($_POST['cargo']);
    $tipo  = definirTipoUsuario($cargo);

    if ($tipo) {
        $sql = "INSERT INTO cadastro_usuario (nome, email, senha, tipo, cargo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nome, $email, $senha, $tipo, $cargo);

        if ($stmt->execute()) {
            echo "<script>alert('Funcionário cadastrado com sucesso!'); window.history.back();</script>";
        } else {
            echo "<script>alert('Erro ao criar um funcionário'); window.history.back();</script> " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "<script>alert('Cargo inválido. Use: GERENTE, SUPERVISOR(A) ou VENDEDOR(A).'); window.history.back();</script>";
    }

    $conn->close();
}
?>
