<?php
session_start();
include_once('ligacao.php');

if(!isset($_GET['id'])){
    echo "Reparação não encontrada.";
    exit();
}

$id = intval($_GET['id']);

// Procura os dados da reparação
$sql = "SELECT * FROM reparacoes WHERE id_reparacao = $id";
$res = mysqli_query($conn, $sql);

if(mysqli_num_rows($res) == 0){
    echo "Reparação não encontrada.";
    exit();
}

$row = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>RegisTech - Ficha da Reparação</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/reparacoes-style.css">
    <style>
        :root { --roxo: #7b2cbf; --roxo-luz: #9d4edd; }
        
        .preco-destaque {
            color: var(--roxo-luz);
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }

        /* Estilo para os Badges de Serviços */
        .badge-servico {
            background: rgba(123, 44, 191, 0.15); 
            color: #e0aaff; 
            padding: 8px 15px; 
            border-radius: 20px; 
            border: 1px solid var(--roxo); 
            font-size: 0.85rem; 
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .badge-servico:hover {
            background: rgba(123, 44, 191, 0.25);
            transform: translateY(-2px);
        }

        .notas-container {
            margin-top: 20px; 
            padding: 15px; 
            background: rgba(255,255,255,0.02); 
            border-radius: 8px; 
            border-left: 4px solid var(--roxo);
        }
    </style>
</head>

<body>

<div class="content" style="max-width:900px; margin:auto; padding-top:60px; padding-bottom: 60px;">

    <div class="titulo-ficha" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 50px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <img src="../imagens_Login/logo.png" alt="Logo" style="width:45px;">
            <h1 style="border-left: 5px solid var(--roxo); padding-left: 15px; margin:0; line-height:1.1; color: white;">
                Ficha da Reparação #<?php echo $row['id_reparacao']; ?>
            </h1>
        </div>
        
        <span style="background: rgba(157, 78, 221, 0.2); color: #e0aaff; padding: 8px 15px; border-radius: 8px; font-size: 0.9rem; font-weight: bold; border: 1px solid rgba(157, 78, 221, 0.3);">
            <?php echo strtoupper($row['estado']); ?>
        </span>
    </div>

    <div class="table-container">

        <div class="cliente-card" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div class="cliente-info">
                <label>ID Reparação</label>
                <p><?php echo $row['id_reparacao']; ?></p>
            </div>

            <div class="cliente-info">
                <label>ID Cliente Associado</label>
                <p>#<?php echo $row['id_cliente']; ?></p>
            </div>

            <div class="cliente-info">
                <label>Equipamento</label>
                <p><?php echo htmlspecialchars($row['equipamento']); ?></p>
            </div>

            <div class="cliente-info">
                <label>Data de Entrada</label>
                <p><?php echo date('d/m/Y', strtotime($row['data_entrada'])); ?></p>
            </div>

            <div class="cliente-info" style="grid-column: span 2;">
                <label>Orçamento Total Estimado</label>
                <div style="background: rgba(0,0,0,0.2); padding: 18px; border-radius: 10px; border: 1px solid rgba(157, 78, 221, 0.2); display: flex; justify-content: space-between; align-items: center;">
                    <p style="margin:0; color: #ccc; font-size: 1rem;">Valor total dos serviços selecionados:</p>
                    <p class="preco-destaque">
                        <?php echo isset($row['preco']) ? number_format($row['preco'], 2, ',', '.') . '€' : '0,00€'; ?>
                    </p>
                </div>
            </div>

            <div class="cliente-info" style="grid-column: span 2; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; border: 1px dashed rgba(157, 78, 221, 0.3);">
                <label style="color: var(--roxo-luz);">Técnico Responsável</label>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px;">
                    <?php if (!empty($row['tecnico_atribuido'])): ?>
                        <p style="margin:0; font-weight: bold; color: #00ff7f; font-size: 1.1rem;">
                            <i class="fas fa-user-check"></i> <?php echo htmlspecialchars($row['tecnico_atribuido']); ?>
                        </p>
                    <?php else: ?>
                        <p style="margin:0; color: #ff4d4d;">⚠️ Reparação pendente de atribuição.</p>
                        <a href="atribuir_reparacao.php?id=<?php echo $row['id_reparacao']; ?>" 
                           style="background: var(--roxo); color: white; text-decoration: none; padding: 8px 15px; border-radius: 6px; font-size: 0.85rem;">
                           <i class="fas fa-hand-sparkles"></i> Reivindicar
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="cliente-info" style="grid-column: span 2; margin-top: 10px;">
                <label>Serviços e Diagnóstico</label>
                
                <?php 
                $descricao = $row['descricao_problema'];
                
                // Se a descrição segue o formato "SERVIÇOS: A, B | Nota"
                if (strpos($descricao, 'Serviços:') !== false && strpos($descricao, '|') !== false) {
                    $partes = explode('|', $descricao);
                    $servicos_raw = str_replace('Serviços:', '', $partes[0]);
                    $servicos_lista = explode(',', $servicos_raw);
                    $notas_adicionais = trim($partes[1]);

                    // Mostrar os Serviços como Badges
                    echo '<div style="display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0;">';
                    foreach ($servicos_lista as $item) {
                        if(trim($item) != "") {
                            echo "<span class='badge-servico'><i class='fas fa-tools'></i> " . htmlspecialchars(trim($item)) . "</span>";
                        }
                    }
                    echo '</div>';
                    
                    // Mostrar a Descrição Manual
                    if(!empty($notas_adicionais)) {
                        echo '<div class="notas-container">';
                        echo '<label style="font-size: 0.7rem; opacity: 0.5; text-transform: uppercase;">Notas Adicionais</label>';
                        echo '<p style="margin:5px 0 0 0; color: #ddd; line-height: 1.5;">' . htmlspecialchars($notas_adicionais) . '</p>';
                        echo '</div>';
                    }
                } else {
                    // Caso seja uma reparação sem o formato de preçário (texto livre)
                    echo '<div style="background: rgba(255,255,255,0.02); padding: 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); margin-top:10px;">';
                    echo '<p style="margin:0; color: #ccc;">' . htmlspecialchars($descricao) . '</p>';
                    echo '</div>';
                }
                ?>
            </div>

        </div>

        <div style="margin-top: 40px; display: flex; gap: 15px;">
            <a href="../meus_pedidos.php" class="btn-add" style="text-decoration: none; background: #333; display: inline-flex; align-items: center; gap: 10px; padding: 12px 25px; border-radius: 8px; color: white; font-weight: bold;">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="editar_meus_reparos.php?id=<?php echo $row['id_reparacao']; ?>" class="btn-add" style="text-decoration: none; background: linear-gradient(135deg, #7b2cbf 0%, #9d4edd 100%); display: inline-flex; align-items: center; gap: 10px; padding: 12px 25px; border-radius: 8px; color: white; font-weight: bold; border: none; cursor: pointer;">
                <i class="fas fa-edit"></i> Editar Ficha
            </a>
        </div>

    </div>
</div>

</body>
</html>