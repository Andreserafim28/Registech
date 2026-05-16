<?php
// Captura as variáveis que o Railway cria automaticamente para o MySQL
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Estabelece a conexão
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("A base de dados ainda está a ligar... Erro: " . mysqli_connect_error());
}
?>