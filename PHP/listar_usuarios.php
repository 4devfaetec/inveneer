<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Sao_Paulo');

include 'conexao.php';

$sql = "SELECT id, nome, email, tipo, cargo, ultimo_acesso FROM cadastro_usuario";
$result = $conn->query($sql);

$usuarios = [];
$agora = new DateTime();

if ($result->num_rows > 0) {
    while ($usuario = $result->fetch_assoc()) {
        // Verifica se ultimo_acesso tem valor válido
        if (!empty($usuario['ultimo_acesso'])) {
            $ultimoAcesso = new DateTime($usuario['ultimo_acesso']);
            $diferenca = $agora->getTimestamp() - $ultimoAcesso->getTimestamp();
            $limiteSegundos = 30 * 60; // 30 minutos
            $status = ($diferenca > $limiteSegundos) ? "Inativo" : "Ativo";
        } else {
            $status = "Inativo"; // ou outro status que preferir para NULL
        }

        $usuarios[] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'cargo' => $usuario['cargo'],
            'status' => $status,
            'ultimo_acesso' => $usuario['ultimo_acesso']
        ];
    }
}

echo json_encode($usuarios);
$conn->close();
