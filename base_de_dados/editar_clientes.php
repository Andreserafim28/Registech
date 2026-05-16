<?php
include_once('ligacao.php');

if(!isset($_GET['id'])){
    echo "Cliente não encontrado.";
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM clientes WHERE id_cliente = $id";
$res = mysqli_query($conn, $sql);

if(mysqli_num_rows($res) == 0){
    echo "Cliente não encontrado.";
    exit();
}

$cliente = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>RegisTech - Editar Cliente</title>
    <link rel="stylesheet" href="../css/clientes-style.css">
</head>
<body>

<div class="content" style="max-width:900px; margin:auto; padding-top:60px;">
    <div class="titulo-ficha">
        <img src="../imagens_Login/logo.png" alt="Logo">
        <h1>Editar Cliente</h1>
    </div>

    <div class="table-container">
        <form action="processar_edicao_cliente.php" method="POST">
            <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">

            <div class="cliente-card">
                <div class="cliente-info">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                </div>

                <div class="cliente-info">
                    <label>NIF (Contribuinte)</label>
                    <input type="text" name="nif" maxlength="9" value="<?php echo htmlspecialchars($cliente['nif'] ?? ''); ?>">
                </div>

                <div class="cliente-info">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>">
                </div>

                <div class="cliente-info">
                    <label>Telefone</label>
                    <input type="text" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone']); ?>">
                </div>

                <div class="cliente-info">
                    <label>Tipo de Cliente</label>
                    <select name="tipo" style="width:100%; padding:12px; border-radius:10px; background:#0f0f18; color:white; border:1px solid rgba(157,78,221,0.25); margin-top:5px;">
                        <option value="Particular" <?php echo ($cliente['tipo'] == 'Particular') ? 'selected' : ''; ?>>Particular</option>
                        <option value="Empresa" <?php echo ($cliente['tipo'] == 'Empresa') ? 'selected' : ''; ?>>Empresa</option>
                    </select>
                </div>

                <div class="cliente-info">
                    <label>Código Postal</label>
                    <input type="text" name="codigo_postal" value="<?php echo htmlspecialchars($cliente['codigo_postal'] ?? ''); ?>">
                </div>

                <div class="cliente-info" style="grid-column: span 2;">
                    <label>Morada</label>
                    <input type="text" name="morada" value="<?php echo htmlspecialchars($cliente['morada']); ?>">
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" name="submit" class="btn-add" style="border: none !important; outline: none !important; background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%) !important; box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3) !important; cursor: pointer;">
                    💾 Guardar Alterações
                </button>
                <a href="../clientes.php" class="btn-cancel">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>