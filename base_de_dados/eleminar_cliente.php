<?php
session_start();
include_once("ligacao.php");

// Verifica se está logado
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Verifica se veio o ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../clientes.php");
    exit();
}

$id = intval($_GET['id']);

// Preparar DELETE (seguro)
$sql = "DELETE FROM clientes WHERE id_cliente = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        // Eliminado com sucesso
        header("Location: ../clientes.php?msg=eliminado");
        exit();
    } else {
        echo "Erro ao eliminar cliente.";
    }

    mysqli_stmt_close($stmt);

} else {
    echo "Erro na preparação da query.";
}

mysqli_close($conn);
?>
