<?php
require_once 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Dados JSON inválidos ou ausentes."
    ]);
    exit;
}

$filtros = $data['filtros'] ?? [];
$gerado_por = $data['gerado_por'] ?? 'Desconhecido';
$nome_relatorio = $data['nome_relatorio'] ?? 'Relatório Solicitado';
$tipo_relatorio = $data['tipo_relatorio'] ?? '';

// Converte filtros para JSON
$filtros_json = json_encode($filtros, JSON_UNESCAPED_UNICODE);
if (!$filtros_json) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao processar filtros."
    ]);
    exit;
}

// Gera código para o relatório
$iniciaisMap = [
    "Relatório de Estoque" => "E",
    "Resumo de Produtos por Categoria" => "C",
    "Resumo de Produtos por Marca" => "M",
    "Produtos Atualizados Hoje" => "A",
    "Resumo Geral de Usuários" => "U",
    "Relatório de Usuários por Cargo" => "G"
];
$inicial = $iniciaisMap[$nome_relatorio] ?? "R";
$codigo = $inicial . rand(1000, 9999);

$stmt = $conn->prepare("INSERT INTO relatorios_solicitados (codigo, nome_relatorio, tipo_relatorio, filtros_json, nome_gerado_por) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Erro na preparação da consulta: " . $conn->error
    ]);
    exit;
}
$stmt->bind_param("sssss", $codigo, $nome_relatorio, $tipo_relatorio, $filtros_json, $gerado_por);

if ($stmt->execute()) {
    // Atualiza estoque apenas se houver novo_estoque
    if (!empty($filtros['novo_estoque']) && is_array($filtros['novo_estoque'])) {
        foreach ($filtros['novo_estoque'] as $produto_id => $novo_estoque) {
            $stmtUpdate = $conn->prepare("UPDATE produtos SET estoque = ? WHERE id = ?");
            if ($stmtUpdate) {
                $stmtUpdate->bind_param("is", $novo_estoque, $produto_id);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }
        }
    }

    echo json_encode([
        "success" => true,
        "message" => "Relatório enviado e estoques atualizados!"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao inserir no banco: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
