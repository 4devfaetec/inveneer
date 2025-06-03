<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['cargo']) || !isset($_SESSION['id'])) {
    http_response_code(401);
    echo "Usuário não autenticado";
    exit;
}

$cargo = $_SESSION['cargo'];
$id = $_SESSION['id'];

// Exemplo: atualiza o último acesso para cargos específicos
if (in_array($cargo, ['GERENTE', 'SUPERVISOR', 'VENDEDOR'])) {
    $stmt = $conn->prepare("UPDATE cadastro_usuario SET ultimo_acesso = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Último acesso atualizado.";
    } else {
        http_response_code(500);
        echo "Erro ao atualizar último acesso.";
    }

    $stmt->close();
} else {
    echo "Cargo não autorizado para atualização de último acesso.";
}

$conn->close();
?>
