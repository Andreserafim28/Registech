<?php
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    // Se isto falhar, o erro 500 aparece. 
    // Temporariamente, podes usar isto para ver o erro no browser:
    exit("Erro de ligação: " . mysqli_connect_error());
}
?>
