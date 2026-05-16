<?php
session_start();

// destruir todas as variáveis de sessão
$_SESSION = [];

// destruir a sessão
session_destroy();

// redirecionar para login
echo "<script>window.location.href='../login.php';</script>";
exit();
?>