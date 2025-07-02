<?php
include "conexao.php";
session_start();

$response = ["status" => "erro", "message" => "Login inválido."];

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
            $_SESSION['tipo'] = $usuario['tipo'];
            $_SESSION['cargo'] = $usuario['cargo'];

            // Atualiza último acesso
            $updateSql = "UPDATE cadastro_usuario SET ultimo_acesso = NOW() WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $usuario['id']);
            $updateStmt->execute();
            $updateStmt->close();

            $response["status"] = "ok";

            // Define para onde redirecionar
            if ($_SESSION['tipo'] === 'ADMIN') {
                $response["redirect"] = "/prototype/app/admin/main.php";                                                                                                                                   
            } elseif ($_SESSION['tipo'] === 'FUNCIONARIO') {
                if ($_SESSION['cargo'] === 'SUPERVISOR') {
                    $response["redirect"] = "../app/user/main.html";
                } elseif ($_SESSION['cargo'] === 'VENDEDOR') {
                    $response["redirect"] = "../app/user/main.html";
                }
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
