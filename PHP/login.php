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
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['tipo'] = strtoupper($usuario['tipo']); // CAPS LOCK
            $_SESSION['cargo'] = strtoupper($usuario['cargo']); // Diferencia Supervisor e Vendedor

            if ($_SESSION['tipo'] === 'ADMIN') {
                // Redireciona para a área de admin (gerente)
                header("Location: ../app/admin/main.html");
            } elseif ($_SESSION['tipo'] === 'FUNCIONARIO') {
                // Se for FUNCIONARIO, verifica o cargo (Supervisor ou Vendedor)
                if ($_SESSION['cargo'] === 'SUPERVISOR') {
                    // Supervisor vai para a área de Supervisor
                    header("Location: ../app/supervisor/main.html");
                } elseif ($_SESSION['cargo'] === 'VENDEDOR') {
                    // Vendedor vai para a área de Vendedor
                    header("Location: ../app/vendedor/main.html");
                } else {
                    echo "<script>alert('Cargo não identificado.'); window.history.back();</script>";
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
