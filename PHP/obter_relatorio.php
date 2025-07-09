<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Sao_Paulo');

include "conexao.php"; // seu arquivo conexão com mysqli

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do relatório não informado']);
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Busca os dados do relatório na tabela relatorios_solicitados
$sql = "SELECT * FROM relatorios_solicitados WHERE id = '$id' LIMIT 1";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Relatório não encontrado']);
    exit;
}

$relatorio = $result->fetch_assoc();

$tipo = $relatorio['tipo_relatorio'];
$filtros_json = $relatorio['filtros_json'];
$filtros = json_decode($filtros_json, true);

$html = ""; // Aqui vai o conteúdo HTML do relatório

switch ($tipo) {
    case 'Estoque':
        $produtosFiltro = $filtros['produtos'] ?? [];

        $sqlProd = "SELECT id, nome, marca, categoria, preco, estoque FROM produtos";
        if (count($produtosFiltro) > 0) {
            $ids = implode("','", array_map([$conn, 'real_escape_string'], $produtosFiltro));
            $sqlProd .= " WHERE id IN ('$ids')";
        }

        $resProd = $conn->query($sqlProd);
        if ($resProd && $resProd->num_rows > 0) {
            $html .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%;'>";
            $html .= "<tr><th>ID</th><th>Nome</th><th>Marca</th><th>Categoria</th><th>Preço</th><th>Estoque</th></tr>";
            while ($row = $resProd->fetch_assoc()) {
                $html .= "<tr>";
                $html .= "<td>" . htmlspecialchars($row['id']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['nome']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['marca']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['categoria']) . "</td>";
                $html .= "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
                $html .= "<td>" . htmlspecialchars($row['estoque']) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html = "<p>Nenhum produto encontrado.</p>";
        }
        break;

    case 'Marca':
        $marcasFiltro = $filtros['marcas'] ?? [];

        $sqlMarca = "SELECT id, nome, marca, categoria, preco, estoque FROM produtos";
        if (count($marcasFiltro) > 0) {
            $marcasEscapadas = array_map([$conn, 'real_escape_string'], $marcasFiltro);
            $inMarcas = "'" . implode("','", $marcasEscapadas) . "'";
            $sqlMarca .= " WHERE marca IN ($inMarcas)";
        }

        $resMarca = $conn->query($sqlMarca);
        if ($resMarca && $resMarca->num_rows > 0) {
            $html .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%;'>";
            $html .= "<tr><th>ID</th><th>Nome</th><th>Marca</th><th>Categoria</th><th>Preço</th><th>Estoque</th></tr>";
            while ($row = $resMarca->fetch_assoc()) {
                $html .= "<tr>";
                $html .= "<td>" . htmlspecialchars($row['id']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['nome']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['marca']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['categoria']) . "</td>";
                $html .= "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
                $html .= "<td>" . htmlspecialchars($row['estoque']) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html = "<p>Nenhum produto encontrado para a marca selecionada.</p>";
        }
        break;

    case 'Atualizados Hoje':
        $hoje = date('Y-m-d');
        $sqlAtualizados = "SELECT id, nome, marca, categoria, preco, estoque FROM produtos WHERE DATE(ultima_atualizacao) = '$hoje'";
        $resAtualizados = $conn->query($sqlAtualizados);

        if ($resAtualizados && $resAtualizados->num_rows > 0) {
            $html .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%;'>";
            $html .= "<tr><th>ID</th><th>Nome</th><th>Marca</th><th>Categoria</th><th>Preço</th><th>Estoque</th></tr>";
            while ($row = $resAtualizados->fetch_assoc()) {
                $html .= "<tr>";
                $html .= "<td>" . htmlspecialchars($row['id']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['nome']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['marca']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['categoria']) . "</td>";
                $html .= "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
                $html .= "<td>" . htmlspecialchars($row['estoque']) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html = "<p>Nenhum produto atualizado hoje.</p>";
        }
        break;

    case 'Categoria':
        $categoriasFiltro = $filtros['categorias'] ?? [];

        $sqlCat = "SELECT id, nome, marca, categoria, preco, estoque FROM produtos";
        if (count($categoriasFiltro) > 0) {
            $categoriasEscapadas = array_map([$conn, 'real_escape_string'], $categoriasFiltro);
            $inCategorias = "'" . implode("','", $categoriasEscapadas) . "'";
            $sqlCat .= " WHERE categoria IN ($inCategorias)";
        }

        $resCat = $conn->query($sqlCat);
        if ($resCat && $resCat->num_rows > 0) {
            $html .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%;'>";
            $html .= "<tr><th>ID</th><th>Nome</th><th>Marca</th><th>Categoria</th><th>Preço</th><th>Estoque</th></tr>";
            while ($row = $resCat->fetch_assoc()) {
                $html .= "<tr>";
                $html .= "<td>" . htmlspecialchars($row['id']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['nome']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['marca']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['categoria']) . "</td>";
                $html .= "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
                $html .= "<td>" . htmlspecialchars($row['estoque']) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html = "<p>Nenhum produto encontrado para a categoria selecionada.</p>";
        }
        break;

    case 'Usuários':
        // Para o relatório geral de usuários, vamos calcular o status "Ativo/Inativo" pelo último acesso
        $sqlUsuarios = "SELECT id, nome, email, cargo, ultimo_acesso FROM cadastro_usuario ORDER BY nome";
        $resUsuarios = $conn->query($sqlUsuarios);

        // Ajusta o fuso horário para América/Sao_Paulo
        $agora = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));

        if ($resUsuarios && $resUsuarios->num_rows > 0) {
            $html .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%;'>";
            $html .= "<tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Cargo</th><th>Status</th><th>Último Acesso</th></tr>";
            while ($row = $resUsuarios->fetch_assoc()) {
                $status = "Inativo";
                if (!empty($row['ultimo_acesso'])) {
                    $ultimoAcesso = new DateTime($row['ultimo_acesso'], new DateTimeZone('America/Sao_Paulo'));
                    $diff = $agora->getTimestamp() - $ultimoAcesso->getTimestamp();
                    $limite = 30 * 60; // 30 minutos
                    $status = ($diff <= $limite) ? "Ativo" : "Inativo";
                }
                $html .= "<tr>";
                $html .= "<td>" . htmlspecialchars($row['id']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['nome']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['email']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['cargo']) . "</td>";
                $html .= "<td>" . $status . "</td>";
                $html .= "<td>" . htmlspecialchars($row['ultimo_acesso']) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html = "<p>Nenhum usuário encontrado.</p>";
        }
        break;

    case 'Cargo':
        // Usuários agrupados por cargo (contagem)
        $sqlCargo = "SELECT cargo, COUNT(*) AS total FROM cadastro_usuario GROUP BY cargo ORDER BY cargo";
        $resCargo = $conn->query($sqlCargo);

        if ($resCargo && $resCargo->num_rows > 0) {
            $html .= "<table border='1' cellpadding='5' cellspacing='0' style='width:50%;'>";
            $html .= "<tr><th>Cargo</th><th>Total de Usuários</th></tr>";
            while ($row = $resCargo->fetch_assoc()) {
                $html .= "<tr>";
                $html .= "<td>" . htmlspecialchars($row['cargo']) . "</td>";
                $html .= "<td>" . htmlspecialchars($row['total']) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html = "<p>Nenhum dado encontrado para cargos.</p>";
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Tipo de relatório desconhecido']);
        exit;
}

echo json_encode([
    'success' => true,
    'relatorio' => [
        'nome' => $relatorio['nome_relatorio'],
        'conteudo_html' => $html,
    ],
]);
