<?php
header('Content-Type: application/json');
include 'conexao.php';

// Recebe JSON do corpo da requisição
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

// Pega os dados do JSON
$id = trim($data['id'] ?? '');
$nome = trim($data['nome_produto'] ?? '');
$marca = trim($data['marca'] ?? '');
$categoria = trim($data['categoria'] ?? '');
$preco = floatval($data['preco'] ?? 0);
$estoque = intval($data['estoque'] ?? -1);

// Validações básicas
if ($id === '' || $nome === '' || $categoria === '' || $preco <= 0 || $estoque < 0) {
    echo json_encode(['success' => false, 'message' => 'Dados obrigatórios inválidos.']);
    exit;
}

// Prepara e executa a query de atualização
$sql = "UPDATE produtos SET nome = ?, marca = ?, categoria = ?, preco = ?, estoque = ?, ultima_atualizacao = NOW() WHERE id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação da query: ' . $conn->error]);
    exit;
}

// Tipos: s = string, d = double, i = int
$stmt->bind_param("sssdis", $nome, $marca, $categoria, $preco, $estoque, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
