<?php
session_start();
include_once('ligacao.php');

// só admin pode alterar
if($_SESSION['tipo'] != 'admin'){
    header("Location: ../staff.php");
    exit();
}

$id = intval($_GET['id']);
$estado = $_GET['estado'];

// estados permitidos
$permitidos = ['ativo', 'ferias', 'baixa'];

if(!in_array($estado, $permitidos)){
    die("Estado inválido");
}

$sql = "UPDATE login SET estado='$estado' WHERE id_user=$id";

if(mysqli_query($conn, $sql)){
    header("Location: ../staff.php");
}else{
    echo "Erro ao atualizar";
}
?>