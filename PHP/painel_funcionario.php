<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: login_form.html");
    exit;
}

echo "Bem-vindo, funcionário " . $_SESSION['nome'] . "!<br>";
echo "<a href='logout.php'>Sair</a>";
?>