<?php
header('Content-Type: application/json');
include 'conexao.php';

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$cargo = strtoupper($_POST['cargo'] ?? '');

if (empty($nome) || empty($email) || empty($senha) || empty($cargo)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
    exit;
}

if ($cargo === 'GERENTE') {
    $tipo = 'ADMIN';
} elseif ($cargo === 'SUPERVISOR' || $cargo === 'VENDEDOR') {
    $tipo = 'FUNCIONARIO';
} else {
    echo json_encode(['success' => false, 'message' => 'Cargo inválido!']);
    exit;
}

// Verifica se email já existe
$stmt = $conn->prepare("SELECT id FROM cadastro_usuario WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email já cadastrado.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Define prefixo baseado no cargo
switch ($cargo) {
    case 'GERENTE': $prefixo = '10'; break;
    case 'SUPERVISOR': $prefixo = '20'; break;
    case 'VENDEDOR': $prefixo = '30'; break;
    default:
        echo json_encode(['success' => false, 'message' => 'Cargo inválido!']);
        $conn->close();
        exit;
}

// Gera ID único
$id = intval($prefixo . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT));
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Insere usuário
$stmt = $conn->prepare("INSERT INTO cadastro_usuario (id, nome, email, senha, tipo, cargo) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $id, $nome, $email, $senhaHash, $tipo, $cargo);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Funcionário adicionado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
exit;
?>
