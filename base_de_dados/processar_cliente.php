<?php
include_once('../base_de_dados/ligacao.php'); // Certifica-te que o caminho está correto

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Receber os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $morada = $_POST['morada'];
    $data_registo = date('Y-m-d'); // Data atual automática

    // 2. Criar o comando SQL (Ajusta os nomes das colunas conforme a tua tabela)
    $sql = "INSERT INTO clientes (nome, email, telefone, morada, data_registo) 
            VALUES ('$nome', '$email', '$telefone', '$morada', '$data_registo')";

    // 3. Executar e verificar
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Cliente adicionado com sucesso!');</script>";
        echo "<script>window.location.href = '../clientes.php';</script>";
        exit();
    } else {
        echo "Erro ao inserir: " . mysqli_error($conn);
    }
}
?>