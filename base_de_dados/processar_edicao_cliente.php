<?php
include_once('ligacao.php');

if(isset($_POST['submit'])){
    $id = intval($_POST['id_cliente']);
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
    $morada = mysqli_real_escape_string($conn, $_POST['morada']);
    $nif = mysqli_real_escape_string($conn, $_POST['nif']);
    $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);
    $cp = mysqli_real_escape_string($conn, $_POST['codigo_postal']);

    $sql = "UPDATE clientes SET 
            nome = '$nome', 
            email = '$email', 
            telefone = '$telefone', 
            morada = '$morada',
            nif = '$nif',
            tipo = '$tipo',
            codigo_postal = '$cp'
            WHERE id_cliente = $id";

    if(mysqli_query($conn, $sql)){
        header("Location: ../clientes.php?msg=sucesso");
    } else {
        echo "Erro ao atualizar: " . mysqli_error($conn);
    }
}
?>