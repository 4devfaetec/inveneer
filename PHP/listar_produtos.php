<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Sao_Paulo');

include 'conexao.php';

$sql = "SELECT id, nome, categoria, preco, estoque, ultima_atualizacao FROM produtos";
$result = $conn->query($sql);

$produtos = [];

if ($result->num_rows > 0) {
    while ($produto = $result->fetch_assoc()) {
        $produtos[] = [
            'id' => $produto['id'],
            'nome' => $produto['nome'],
            'categoria' => $produto['categoria'],
            'preco' => $produto['preco'],
            'estoque' => $produto['estoque'],
            'ultima_atualizacao' => $produto['ultima_atualizacao']
        ];
    }
}

echo json_encode($produtos);
$conn->close();

