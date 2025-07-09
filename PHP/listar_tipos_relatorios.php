<?php
include 'conexao.php';

header('Content-Type: application/json');

// Pega os tipos distintos da coluna tipo_relatorio da tabela relatorios_solicitados
$sql = "SELECT DISTINCT tipo_relatorio FROM relatorios_solicitados ORDER BY tipo_relatorio ASC";

$result = $conn->query($sql);

$tipos = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tipos[] = $row['tipo_relatorio'];
    }
}

echo json_encode([
    'success' => true,
    'tipos' => $tipos
]);

$conn->close();
?>
