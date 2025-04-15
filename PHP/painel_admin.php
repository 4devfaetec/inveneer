<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login_form.html");
    exit;
}

echo "Bem-vindo, administrador " . $_SESSION['nome'] . "!<br>";
echo "<a href='cadastrar_funcionario.php'>Cadastrar funcionário</a><br>";
echo "<a href='listar_funcionarios.php'>Listar funcionários</a><br>";
echo "<a href='logout.php'>Sair</a>";
?>