<?php
include "conexao.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            // Atualiza o último acesso
            $updateSql = "UPDATE cadastro_usuario SET ultimo_acesso = NOW() WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $usuario['id']);
            $updateStmt->execute();
            $updateStmt->close();

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['tipo'] = $usuario['tipo'];  // ADMIN ou FUNCIONARIO
            $_SESSION['cargo'] = $usuario['cargo'];  // GERENTE, SUPERVISOR ou VENDEDOR

            if ($_SESSION['tipo'] === 'ADMIN') {
                header("Location: ../app/admin/main.php");
            } elseif ($_SESSION['tipo'] === 'FUNCIONARIO') {
                if ($_SESSION['cargo'] === 'SUPERVISOR') {
                    header("Location: ../app/admin/main.php");
                } elseif ($_SESSION['cargo'] === 'VENDEDOR') {
                    header("Location: ../app/user/main.html");
                }
            }
            exit;
        } else {
            echo "<script>alert('Login inválido.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Login inválido.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Acesso inválido.'); window.history.back();</script>";
}
?>
