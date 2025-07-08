<?php
session_start();
header('Content-Type: application/json');
include_once 'conexao.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['erro' => 'Usuário não autenticado']);
    exit;
}

$usuarioId = $_SESSION['id'];

$sql = "SELECT nome, tipo, cargo FROM cadastro_usuario WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['erro' => 'Usuário não encontrado']);
    exit;
}

$usuario = $result->fetch_assoc();

echo json_encode([
    'nome' => $usuario['nome'],
    'tipo' => $usuario['tipo'],
    'cargo' => $usuario['cargo']
]);
