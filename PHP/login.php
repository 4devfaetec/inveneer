<?php
include "conexao.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifique se os campos estão sendo preenchidos no formulário
    if (isset($_POST['email']) && isset($_POST['senha'])) {
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
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['tipo'] = strtoupper($usuario['tipo']); // CAPS LOCK

                if (!in_array($_SESSION['tipo'], ['ADMIN', 'FUNCIONARIO'])) {
                    echo "<script>alert('Tipo de usuário inválido.'); window.history.back();</script>";
                    exit;
                }

                if ($_SESSION['tipo'] === 'ADMIN') {
                    header("Location: ../app/admin/main.html"); // Altere para a página do admin
                } elseif ($_SESSION['tipo'] === 'FUNCIONARIO') {
                    header("Location: painel_funcionario.php");
                } else {
                    echo "Tipo de usuário inválido.";
                }
            } else {
                echo "<script>alert('Senha incorreta.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Email não encontrado.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Campos não preenchidos corretamente.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Acesso inválido.'); window.history.back();</script>";
}
?>
