<?php
// Configurações para o Railway
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Estabelece a conexão usando a porta correta
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}
?>
