<?php
session_start();
include_once('ligacao.php');

if(!isset($_GET['id'])){
    echo "Cliente não encontrado.";
    exit();
}

$id = intval($_GET['id']);

// Busca os dados do cliente
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
    <title>RegisTech - Ficha do Cliente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/clientes-style.css">
</head>

<body>

<div class="content" style="max-width:900px; margin:auto; padding-top:60px; padding-bottom: 80px;">

    <div class="titulo-ficha" style="display: flex; align-items: center; gap: 15px; margin-bottom: 50px;">
        <img src="../imagens_Login/logo.png" alt="Logo RegisTech" style="width:50px;">
        <h1 style="border-left: 5px solid #7b2cbf; padding-left: 15px; margin:0; color: white;">
            Ficha do Cliente: <?php echo htmlspecialchars($cliente['nome']); ?>
        </h1>
    </div>

    <div class="table-container">

        <div class="cliente-card">
            <div class="cliente-info">
                <label>Nº Cliente</label>
                <p>#<?php echo $cliente['id_cliente']; ?></p>
            </div>

            <div class="cliente-info">
                <label>Nome Completo</label>
                <p><?php echo htmlspecialchars($cliente['nome']); ?></p>
            </div>

            <div class="cliente-info">
                <label>NIF (Contribuinte)</label>
                <p><?php echo htmlspecialchars($cliente['nif'] ?? 'Não registado'); ?></p>
            </div>

            <div class="cliente-info">
                <label>Tipo de Cliente</label>
                <p><span style="background: rgba(123, 44, 191, 0.2); color: #d8b4fe; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: bold;">
                    <?php echo strtoupper($cliente['tipo']); ?>
                </span></p>
            </div>

            <div class="cliente-info">
                <label>Email</label>
                <p><?php echo htmlspecialchars($cliente['email']); ?></p>
            </div>

            <div class="cliente-info">
                <label>Telefone</label>
                <p><?php echo htmlspecialchars($cliente['telefone']); ?></p>
            </div>

            <div class="cliente-info">
                <label>Código Postal</label>
                <p><?php echo htmlspecialchars($cliente['codigo_postal'] ?? '---- ---'); ?></p>
            </div>

            <div class="cliente-info">
                <label>Data Registo</label>
                <p><?php echo date('d/m/Y', strtotime($cliente['data_registo'])); ?></p>
            </div>

            <div class="cliente-info" style="grid-column: span 2;">
                <label>Morada</label>
                <p><?php echo htmlspecialchars($cliente['morada']); ?></p>
            </div>
        </div>

        <div style="margin-top: 50px; background: rgba(26, 26, 46, 0.4); border-radius: 16px; padding: 25px; border: 1px solid rgba(157, 78, 221, 0.2);">
            <h3 style="color: #9d4edd; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; font-size: 1.2rem;">
                <i class="fas fa-history"></i> Histórico de Reparações
            </h3>

            <table style="width: 100%; border-collapse: collapse; color: white;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid rgba(123, 44, 191, 0.3);">
                        <th style="padding: 12px; font-size: 0.85rem; color: #d8b4fe;">ID</th>
                        <th style="padding: 12px; font-size: 0.85rem; color: #d8b4fe;">EQUIPAMENTO</th>
                        <th style="padding: 12px; font-size: 0.85rem; color: #d8b4fe;">DATA</th>
                        <th style="padding: 12px; font-size: 0.85rem; color: #d8b4fe;">ESTADO</th>
                        <th style="padding: 12px; font-size: 0.85rem; color: #d8b4fe;">AÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Busca todas as reparações deste cliente específico
                    $sql_reps = "SELECT * FROM reparacoes WHERE id_cliente = $id ORDER BY data_entrada DESC";
                    $res_reps = mysqli_query($conn, $sql_reps);

                    if (mysqli_num_rows($res_reps) > 0) {
                        while ($rep = mysqli_fetch_assoc($res_reps)) {
                            // Define cor do estado
                            $cor_estado = ($rep['estado'] == 'Concluído') ? '#00ff7f' : '#e0aaff';
                    ?>
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: 0.3s;" onmouseover="this.style.background='rgba(157, 78, 221, 0.05)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 15px;">#<?php echo $rep['id_reparacao']; ?></td>
                            <td style="padding: 15px; font-weight: 500;"><?php echo htmlspecialchars($rep['equipamento']); ?></td>
                            <td style="padding: 15px; font-size: 0.9rem;"><?php echo date('d/m/Y', strtotime($rep['data_entrada'])); ?></td>
                            <td style="padding: 15px;">
                                <span style="color: <?php echo $cor_estado; ?>; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?php echo htmlspecialchars($rep['estado']); ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <a href="ver_reparacao.php?id=<?php echo $rep['id_reparacao']; ?>" style="color: #9d4edd; text-decoration: none; font-size: 0.9rem; font-weight: bold;">
                                    <i class="fas fa-eye"></i> Detalhes
                                </a>
                            </td>
                        </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='5' style='padding: 30px; text-align: center; color: #64748b; font-style: italic;'>Este cliente ainda não possui registos de reparação no sistema.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <br><br>

        <div style="display: flex; gap: 15px;">
            <a href="../clientes.php" class="btn-add" style="text-decoration: none; background: #333; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-arrow-left"></i> Voltar aos Clientes
            </a>
        </div>

    </div>

</div>

</body>
</html>