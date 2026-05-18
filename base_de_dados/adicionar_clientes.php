<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

include_once('ligacao.php');

if(isset($_POST['submit'])){
    $nome          = mysqli_real_escape_string($conn, $_POST['nome']);
    $email         = mysqli_real_escape_string($conn, $_POST['email']);
    $nif           = mysqli_real_escape_string($conn, $_POST['nif']);
    $tipo          = mysqli_real_escape_string($conn, $_POST['tipo']);
    $telefone      = mysqli_real_escape_string($conn, $_POST['telefone']);
    $morada        = mysqli_real_escape_string($conn, $_POST['morada']);
    $codigo_postal = mysqli_real_escape_string($conn, $_POST['codigo_postal']);
    $data_registo  = date("Y-m-d");

    $sql = "INSERT INTO clientes (nome, email, nif, tipo, telefone, morada, codigo_postal, data_registo) 
            VALUES ('$nome', '$email', '$nif', '$tipo', '$telefone', '$morada', '$codigo_postal', '$data_registo')";

    if(mysqli_query($conn, $sql)){
        header("Location: ../clientes.php");
        exit();
    } else {
        echo "Erro: " . mysqli_error($conn);
    }
}
include_once('discord.php'); // Adiciona isto no topo

// ... código do INSERT ...
if (mysqli_query($conn, $sql)) {
    enviarNotificacaoDiscord("👤 **Novo Utilizador:** O admin acaba de criar a conta para o técnico **$username**.");
}

    
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>RegisTech - Adicionar Cliente</title>
    <!-- Mudei para o caminho que parece ser o teu padrão -->
    <link rel="stylesheet" href="../css/home-style.css"> 
    <link rel="stylesheet" href="../css/style-geral.css">
    <link rel="stylesheet" href="../css/reparacoes-style.css">
</head>
<body>

    <div class="content">
        <div class="content-header">
            <h1>Adicionar Novo Cliente</h1>
        </div>

        <form method="POST">
            <!-- Usando a tua classe cliente-card que já tens no CSS -->
            <div class="cliente-card">
                
                <div class="cliente-info">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" required placeholder="Ex: João Silva">
                </div>

                <div class="cliente-info">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="exemplo@dominio.com">
                </div>

                <div class="cliente-info">
                    <label>NIF (Contribuinte)</label>
                    <input type="text" name="nif" maxlength="9" placeholder="9 dígitos">
                </div>

                <div class="cliente-info">
                    <label>Tipo de Cliente</label>
                    <select name="tipo">
                        <option value="Particular">Particular</option>
                        <option value="Empresa">Empresa</option>
                    </select>
                </div>

                <div class="cliente-info">
                    <label>Telefone</label>
                    <input type="text" name="telefone" required placeholder="9xx xxx xxx">
                </div>

                <div class="cliente-info">
                    <label>Código Postal</label>
                    <input type="text" name="codigo_postal" placeholder="0000-000">
                </div>

                <!-- Morada a ocupar a largura total (span 2) -->
                <div class="cliente-info" style="grid-column: span 2;">
                    <label>Morada</label>
                    <input type="text" name="morada" placeholder="Rua, Porta, Localidade...">
                </div>

            </div>

            <div class="form-buttons">
                <button type="submit" name="submit" class="btn-add">
                    Guardar Cliente
                </button>
                <a href="../clientes.php" class="btn-cancel">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

</body>
</html>
