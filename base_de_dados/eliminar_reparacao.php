<?php

include_once('ligacao.php');

$id = $_GET['id'];

$sql = "DELETE FROM reparacoes WHERE id_reparacao = $id";

mysqli_query($conn,$sql);

header("Location: ../reparacoes.php");

?>