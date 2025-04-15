<?php
include "conexao.php";
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login_form.html");
    exit;
}

$sql = "SELECT id, nome, email FROM usuarios WHERE tipo = 'funcionario'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($func = $result->fetch_assoc()) {
        echo "ID: {$func['id']} - Nome: {$func['nome']} - Email: {$func['email']}<br>";
    }
} else {
    echo "Nenhum funcionário encontrado.";
}
?>