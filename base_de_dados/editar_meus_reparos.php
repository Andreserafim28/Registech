<?php
session_start();
include_once('ligacao.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id == 0) { header("Location: ../reparacoes.php"); exit(); }

$sql = "SELECT * FROM reparacoes WHERE id_reparacao = $id";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

$sql_servicos = "SELECT * FROM servicos ORDER BY nome_servico ASC";
$res_servicos = mysqli_query($conn, $sql_servicos);

if (isset($_POST['submit'])) {
    $equipamento = mysqli_real_escape_string($conn, $_POST['equipamento']);
    $estado = mysqli_real_escape_string($conn, $_POST['estado']);
    $preco_total = $_POST['preco_total']; 
    $problema_manual = mysqli_real_escape_string($conn, $_POST['problema_manual']);

    $servicos_selecionados = isset($_POST['servicos']) ? $_POST['servicos'] : [];
    $texto_servicos = !empty($servicos_selecionados) ? "Serviços: " . implode(", ", $servicos_selecionados) . " | " : "";
    $descricao_final = $texto_servicos . $problema_manual;

    $sql_update = "UPDATE reparacoes SET equipamento='$equipamento', descricao_problema='$descricao_final', estado='$estado', preco='$preco_total' WHERE id_reparacao = $id";

    if (mysqli_query($conn, $sql_update)) {
        header("Location: ../reparacoes.php?msg=editado");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>RegisTech - Editar Reparação</title>
    <link rel="stylesheet" href="../css/reparacoes-style.css">
    <style>
        :root { --roxo: #7b2cbf; --roxo-luz: #9d4edd; --fundo-card: #161625; }
        body { background-color: #0b0e14; color: white; font-family: 'Segoe UI', sans-serif; }
        .content { max-width: 900px; margin: 40px auto; padding: 20px; }
        
        .cliente-card { 
            background: var(--fundo-card); padding: 30px; border-radius: 15px; 
            border: 1px solid rgba(255, 255, 255, 0.05); display: grid; 
            grid-template-columns: 1fr 1fr; gap: 20px; 
        }

        .cliente-info label { display: block; color: var(--roxo-luz); margin-bottom: 8px; font-weight: bold; font-size: 0.85rem; text-transform: uppercase; }
        select, input[type="text"], textarea { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; background: #1a1a2e; color: white; outline: none; }

        /* --- PREÇÁRIO --- */
        .grid-servicos { grid-column: span 2; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px; max-height: 220px; overflow-y: auto; border: 1px solid rgba(157,78,221,0.1); }
        .servico-item { display: flex; align-items: center; padding: 10px 15px; border-radius: 6px; cursor: pointer; transition: 0.2s; margin-bottom: 5px; }
        .servico-item.active { background: rgba(123, 44, 191, 0.1); border: 1px solid rgba(123,44,191,0.3); }
        .preco-box { min-width: 70px; color: var(--roxo-luz); font-weight: bold; margin-right: 15px; text-align: right; }
        .check-custom { width: 18px; height: 18px; border: 2px solid #555; border-radius: 4px; margin-right: 15px; position: relative; flex-shrink: 0; }
        input[type="checkbox"] { display: none; }
        input[type="checkbox"]:checked + .check-custom { background: var(--roxo); border-color: var(--roxo); }
        input[type="checkbox"]:checked + .check-custom::after { content: '✓'; position: absolute; color: white; font-size: 12px; left: 3px; top: -1px; }

        /* --- DESIGN IGUAL AO 'VER' --- */
        .preco-destaque { 
            margin: 0; 
            color: var(--roxo-luz); 
            font-size: 1.5rem; 
            font-weight: bold; 
            
        }

        .form-buttons { margin-top: 10px; display: flex; gap: 15px; grid-column: span 2; }
        .btn-save { background: linear-gradient(135deg, #7b2cbf 0%, #9d4edd 100%); color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .btn-cancel { background: transparent; color: #ccc; padding: 12px 25px; border: 1px solid #444; border-radius: 8px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="content">
    <h1 style="border-left: 5px solid var(--roxo); padding-left: 15px; margin-bottom: 30px;">Editar Reparação #<?php echo $id; ?></h1>

    <form method="POST">
        <div class="cliente-card">
            <div class="cliente-info">
                <label>Equipamento</label>
                <input type="text" name="equipamento" value="<?php echo htmlspecialchars($row['equipamento']); ?>" required>
            </div>

            <div class="cliente-info">
                <label>Estado Atual</label>
                <select name="estado">
                    <?php 
                    $estados = ["Em diagnóstico", "Em reparação", "A aguardar peça", "Concluído", "Concluído e entregue"];
                    foreach($estados as $e) { $sel = ($row['estado'] == $e) ? "selected" : ""; echo "<option value='$e' $sel>$e</option>"; }
                    ?>
                </select>
            </div>

            <div class="cliente-info" style="grid-column: span 2;">
                <label>Serviços do Preçário</label>
                <div class="grid-servicos">
                    <?php 
                    $desc_completa = $row['descricao_problema'];
                    $notas_manuais = $desc_completa;
                    $servicos_ja_escolhidos = [];
                    if (strpos($desc_completa, 'Serviços:') !== false && strpos($desc_completa, '|') !== false) {
                        $partes = explode('|', $desc_completa);
                        $servicos_raw = str_replace('Serviços:', '', $partes[0]);
                        $servicos_ja_escolhidos = array_map('trim', explode(',', $servicos_raw));
                        $notas_manuais = trim($partes[1]);
                    }

                    while($serv = mysqli_fetch_assoc($res_servicos)): 
                        $checked = in_array($serv['nome_servico'], $servicos_ja_escolhidos) ? "checked" : "";
                        $active_class = $checked ? "active" : "";
                    ?>
                        <label class="servico-item <?php echo $active_class; ?>" id="item-<?php echo $serv['id_servico']; ?>">
                            <div class="preco-box"><?php echo number_format($serv['preco'], 2); ?>€</div>
                            <input type="checkbox" name="servicos[]" value="<?php echo $serv['nome_servico']; ?>" data-preco="<?php echo $serv['preco']; ?>" <?php echo $checked; ?> onchange="atualizar(this, 'item-<?php echo $serv['id_servico']; ?>')">
                            <span class="check-custom"></span>
                            <span style="color: #ddd; flex-grow: 1;"><?php echo $serv['nome_servico']; ?></span>
                        </label>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="cliente-info" style="grid-column: span 2;">
                <label>Orçamento Total Estimado</label>
                <div style="background: rgba(0,0,0,0.2); padding: 18px; border-radius: 10px; border: 1px solid rgba(157, 78, 221, 0.2); display: flex; justify-content: space-between; align-items: center;">
                    <p style="margin:0; color: #ccc; font-size: 1rem;">Valor total dos serviços selecionados:</p>
                    <p class="preco-destaque" id="totalHtml">
                        <?php echo number_format($row['preco'], 2, ',', '.') . '€'; ?>
                    </p>
                </div>
                <input type="hidden" name="preco_total" id="inputPreco" value="<?php echo $row['preco']; ?>">
            </div>

            <div class="cliente-info" style="grid-column: span 2;">
                <label>Notas Adicionais / Diagnóstico</label>
                <textarea name="problema_manual" rows="3"><?php echo htmlspecialchars($notas_manuais); ?></textarea>
            </div>

            <div class="form-buttons">
                <button type="submit" name="submit" class="btn-save">💾 Guardar Alterações</button>
                <a href="../reparacoes.php" class="btn-cancel">Cancelar</a>
            </div>
        </div>
    </form>
</div>

<script>
function atualizar(el, id) {
    document.getElementById(id).classList.toggle('active', el.checked);
    let total = 0;
    document.querySelectorAll('input[type="checkbox"]:checked').forEach(i => {
        total += parseFloat(i.getAttribute('data-preco'));
    });
    // Formata o preço para o padrão português (0,00€)
    document.getElementById('totalHtml').innerText = total.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '€';
    document.getElementById('inputPreco').value = total.toFixed(2);
}
</script>
</body>
</html>