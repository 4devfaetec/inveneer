<?php
header('Content-Type: application/json');
include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome_produto'] ?? '';
    $marca = $_POST['marca'] ?? '';            // <<< campo novo
    $categoria = $_POST['categoria'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $estoque = $_POST['estoque'] ?? '';

    if (empty($nome) || empty($marca) || empty($categoria) || empty($preco) || empty($estoque)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
        exit;
    }

    $iniciaisCategoria = [
        'Eletrônicos' => 'ELE',
        'Informática' => 'INF',
        'Eletrodomésticos' => 'EDM',
        'Casa' => 'CAS',
        'Cozinha' => 'COZ',
        'Escritório' => 'ESC',
        'Acessórios' => 'ACE'
    ];

    $iniciais = $iniciaisCategoria[$categoria] ?? 'GEN';

    $numero = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $id = $iniciais . $numero;

    // Verifica se ID já existe (opcional)
    $check = $conn->prepare("SELECT id FROM produtos WHERE id = ?");
    $check->bind_param("s", $id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Produto já cadastrado com esse ID. Tente novamente.']);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    $sql = "INSERT INTO produtos (id, nome, marca, categoria, preco, estoque) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdi", $id, $nome, $marca, $categoria, $preco, $estoque);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Produto cadastrado com sucesso!' ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida!']);
}
?>
