<?php
include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome_produto'];
    $categoria = $_POST['categoria'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];

    // Mapa de iniciais fixas para cada categoria
    $iniciaisCategoria = [
        'Eletrônicos' => 'ELE',
        'Informática' => 'INF',
        'Eletrodomésticos' => 'EDM',
        'Casa' => 'CAS',
        'Cozinha' => 'COZ',
        'Escritório' => 'ESC',
        'Acessórios' => 'ACE'
    ];

    // Busca as iniciais corretas
    if (isset($iniciaisCategoria[$categoria])) {
        $iniciais = $iniciaisCategoria[$categoria];
    } else {
        $iniciais = 'GEN'; // Caso não esteja no mapa
    }

    // Gera número aleatório de 4 dígitos
    $numero = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

    // Monta ID final
    $id = $iniciais . $numero;

    // Prepara SQL
    $sql = "INSERT INTO produtos (id, nome, categoria, preco, estoque) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdi", $id, $nome, $categoria, $preco, $estoque);

    if ($stmt->execute()) {
        echo "Produto cadastrado com sucesso! ID: " . $id;
    } else {
        echo "Erro ao cadastrar produto: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Requisição inválida!";
}
?>
