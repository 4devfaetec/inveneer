<?php
include 'conexao.php';

header('Content-Type: application/json');

// Recebe os filtros via GET, se existir
$tipo = isset($_GET['tipo']) && $_GET['tipo'] !== '' ? $conn->real_escape_string($_GET['tipo']) : null;
$data_inicial = isset($_GET['data_inicial']) && $_GET['data_inicial'] !== '' ? $conn->real_escape_string($_GET['data_inicial']) : null;
$data_final = isset($_GET['data_final']) && $_GET['data_final'] !== '' ? $conn->real_escape_string($_GET['data_final']) : null;

// Monta a query base com condição sempre verdadeira para facilitar concatenação
$sql = "SELECT id, codigo, nome_relatorio, tipo_relatorio, nome_gerado_por, data_criacao 
        FROM relatorios_solicitados 
        WHERE 1=1 ";

// Adiciona filtro tipo, se passado
if ($tipo) {
    $sql .= " AND tipo_relatorio = '$tipo' ";
}

// Adiciona filtro data inicial, se passado
if ($data_inicial) {
    // Adiciona a partir da meia-noite do dia informado
    $sql .= " AND data_criacao >= '$data_inicial 00:00:00' ";
}

// Adiciona filtro data final, se passado
if ($data_final) {
    // Adiciona até o fim do dia informado
    $sql .= " AND data_criacao <= '$data_final 23:59:59' ";
}

$sql .= " ORDER BY data_criacao DESC";

$result = $conn->query($sql);

$relatorios = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $relatorios[] = [
            'id' => $row['id'],
            'codigo' => $row['codigo'],
            'nome' => $row['nome_relatorio'],
            'tipo' => $row['tipo_relatorio'],
            'gerado_por' => $row['nome_gerado_por'],
            'data' => $row['data_criacao']
        ];
    }
}

echo json_encode(['success' => true, 'relatorios' => $relatorios]);

$conn->close();
?>
