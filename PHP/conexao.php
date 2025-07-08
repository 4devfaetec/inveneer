<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "usuario";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Define o charset para UTF-8
$conn->set_charset("utf8");
?>