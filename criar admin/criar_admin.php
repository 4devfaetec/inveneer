<?php
include "../PHP/conexao.php";

// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);  // Criptografar a senha
    $tipo  = $_POST['tipo'];

    // Preparar a consulta SQL para evitar SQL Injection
    $sql = "INSERT INTO cadastro_usuario (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $senha, $tipo);

    // Executar a consulta e verificar o sucesso
    if ($stmt->execute()) {
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar usuário: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Acesso inválido.";
}
?>
