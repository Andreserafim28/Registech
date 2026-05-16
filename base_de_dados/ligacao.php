<?php
// Ativar exibição de erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

echo "Tentando ligar a: $host na porta $port...<br>";

if (!function_exists('mysqli_connect')) {
    die("ERRO CRÍTICO: A extensão MySQLi ainda não está ativa no servidor!");
}

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
} else {
    echo "Ligação estabelecida com sucesso!";
}
?>
