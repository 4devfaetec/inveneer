<?php
include 'conexao.php';  // inclui conexão com o banco

// Recebe dados do POST
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$cargo = strtoupper($_POST['cargo']); // deixa maiúsculo para padronizar

// Define tipo conforme cargo
if ($cargo === 'GERENTE') {
    $tipo = 'ADMIN';
} elseif ($cargo === 'SUPERVISOR' || $cargo === 'VENDEDOR') {
    $tipo = 'FUNCIONARIO';
} else {
    die("Cargo inválido!");
}

// Validação básica
if (empty($nome) || empty($email) || empty($senha) || empty($cargo)) {
    die("Por favor, preencha todos os campos.");
}

// Verifica se email já existe
$stmt = $conn->prepare("SELECT id FROM cadastro_usuario WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die("Email já cadastrado.");
}
$stmt->close();

// Prefixo baseado no cargo para o id
switch ($cargo) {
    case 'GERENTE':
        $prefixo = '10';
        break;
    case 'SUPERVISOR':
        $prefixo = '20';
        break;
    case 'VENDEDOR':
        $prefixo = '30';
        break;
    default:
        die('Cargo inválido!');
}

// Gera ID personalizado — inteiro
$id = intval($prefixo . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT));

// Criptografa senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Insere no banco com id manual
$stmt = $conn->prepare("INSERT INTO cadastro_usuario (id, nome, email, senha, tipo, cargo) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $id, $nome, $email, $senhaHash, $tipo, $cargo);

if ($stmt->execute()) {
    echo "Usuário cadastrado com sucesso! ID: $id, Cargo: $cargo, Tipo: $tipo";
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
