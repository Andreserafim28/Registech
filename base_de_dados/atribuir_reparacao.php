<?php
session_start();
include_once('ligacao.php'); // Confirma se o caminho da ligação está certo aqui

if(isset($_GET['id']) && isset($_SESSION['username'])){
    
    $id_reparacao = intval($_GET['id']);
    $nome_tecnico = $_SESSION['username']; 

    // Atualiza a BD
    $sql = "UPDATE reparacoes 
            SET tecnico_atribuido = '$nome_tecnico', 
                estado = 'Em Reparação' 
            WHERE id_reparacao = $id_reparacao";

    if(mysqli_query($conn, $sql)){
        // Redireciona de volta para a ficha (ajusta o caminho se necessário)
        header("Location: ver_reparacao.php?id=$id_reparacao"); 
        exit();
    } else {
        echo "Erro: " . mysqli_error($conn);
    }
} else {
    echo "Erro: ID ou Sessão em falta.";
}
?>