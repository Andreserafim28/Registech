<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "registech_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Erro na ligação: " . mysqli_connect_error());
}
?>
