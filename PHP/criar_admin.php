<?php
include "conexao.php";

// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo  = $_POST['tipo'];

    $sql = "INSERT INTO cadastro_usuario (nome, email, senha, tipo) 
            VALUES ('$nome', '$email', '$senha', '$tipo')";

    if ($conn->query($sql) === TRUE) {
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar usuário: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Acesso inválido.";
}
?>
