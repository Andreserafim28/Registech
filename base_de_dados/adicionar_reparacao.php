<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: ../login.php"); exit(); }
include_once('ligacao.php');
include_once('discord.php');

$sql_clientes = "SELECT id_cliente, nome FROM clientes ORDER BY nome ASC";
$res_clientes = mysqli_query($conn, $sql_clientes);

$sql_servicos = "SELECT * FROM servicos ORDER BY nome_servico ASC";
$res_servicos = mysqli_query($conn, $sql_servicos);

if(isset($_POST['submit'])){
    $cliente_id = $_POST['cliente']; 
    $equipamento = $_POST['equipamento'];
    $problema_manual = $_POST['problema']; 
    $estado = $_POST['estado'];
    $preco_total = $_POST['preco_total']; 
    $data = date("Y-m-d");

    $servicos_selecionados = isset($_POST['servicos']) ? $_POST['servicos'] : [];
    $texto_servicos = !empty($servicos_selecionados) ? "Serviços: " . implode(", ", $servicos_selecionados) . " | " : "";
    $descricao_final = mysqli_real_escape_string($conn, $texto_servicos . $problema_manual);

    // Certifica-te que a coluna se chama 'preco' na tua tabela 'reparacoes'
    $sql = "INSERT INTO reparacoes (id_cliente, equipamento, descricao_problema, estado, data_entrada, preco) 
            VALUES ('$cliente_id', '$equipamento', '$descricao_final', '$estado', '$data', '$preco_total')";

    if(mysqli_query($conn, $sql)){ header("Location: ../reparacoes.php"); exit(); }
}


// ... após o sucesso do registo ...
$texto = "🛠️ **Entrada de Equipamento:**\n- **Equipamento:** $equipamento\n- **Cliente:** $nome_cliente";
enviarNotificacaoDiscord($texto);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>RegisTech - Nova Reparação</title>
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
        
        select, input[type="text"] { 
            width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; 
            background: #1a1a2e; color: white; outline: none; transition: 0.3s;
        }

        /* --- ESTILO DAS CHECKBOXES --- */
        .grid-servicos { grid-column: span 2; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px; max-height: 220px; overflow-y: auto; border: 1px solid rgba(157,78,221,0.1); }
        .servico-item { display: flex; align-items: center; padding: 10px 15px; border-radius: 6px; cursor: pointer; transition: 0.2s; margin-bottom: 5px; }
        .servico-item:hover { background: rgba(255,255,255,0.03); }
        .servico-item.active { background: rgba(123, 44, 191, 0.1); border: 1px solid rgba(123,44,191,0.3); }
        .preco-box { min-width: 70px; color: var(--roxo-luz); font-weight: bold; font-size: 0.95rem; margin-right: 15px; text-align: right; }
        .check-custom { width: 18px; height: 18px; border: 2px solid #555; border-radius: 4px; margin-right: 15px; display: inline-block; position: relative; transition: 0.3s; flex-shrink: 0; }
        input[type="checkbox"] { display: none; }
        input[type="checkbox"]:checked + .check-custom { background: var(--roxo); border-color: var(--roxo); box-shadow: 0 0 8px var(--roxo); }
        input[type="checkbox"]:checked + .check-custom::after { content: '✓'; position: absolute; color: white; font-size: 12px; left: 3px; top: -1px; }

        /* --- BLOCOS DE ESTADO E PREÇO --- */
        .bloco-separado { background: rgba(0,0,0,0.2); padding: 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.03); }
        .total-valor { color: white; font-size: 1.8rem; font-weight: bold; margin: 5px 0 0 0; text-align: right; }

        /* --- BOTÕES IGUAIS À PÁGINA DE EDITAR --- */
        .form-buttons { margin-top: 30px; display: flex; gap: 15px; justify-content: flex-start; }

        .btn-save {
            background: linear-gradient(135deg, #7b2cbf 0%, #9d4edd 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            font-size: 0.95rem;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(123, 44, 191, 0.4);
        }

        .btn-cancel {
            background: transparent;
            color: #ccc;
            padding: 12px 25px;
            border: 1px solid #444;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            font-size: 0.95rem;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border-color: #666;
        }
    </style>
</head>
<body>
<div class="content">
    <h1 style="border-left: 5px solid var(--roxo); padding-left: 15px; margin-bottom: 30px;">Nova Reparação</h1>
    <form method="POST">
        <div class="cliente-card">
            <div class="cliente-info">
                <label>Cliente</label>
                <select name="cliente" required>
                    <option value="">-- Selecionar --</option>
                    <?php while($cli = mysqli_fetch_assoc($res_clientes)) echo "<option value='".$cli['id_cliente']."'>".$cli['nome']."</option>"; ?>
                </select>
            </div>
            <div class="cliente-info">
                <label>Equipamento</label>
                <input type="text" name="equipamento" placeholder="Ex: iPhone 14" required>
            </div>

            <div class="cliente-info" style="grid-column: span 2;">
                <label>Serviços do Preçário</label>
                <div class="grid-servicos">
                    <?php while($serv = mysqli_fetch_assoc($res_servicos)): ?>
                        <label class="servico-item" id="item-<?php echo $serv['id_servico']; ?>">
                            <div class="preco-box"><?php echo number_format($serv['preco'], 2); ?>€</div>
                            <input type="checkbox" name="servicos[]" value="<?php echo $serv['nome_servico']; ?>" data-preco="<?php echo $serv['preco']; ?>" onchange="atualizar(this, 'item-<?php echo $serv['id_servico']; ?>')">
                            <span class="check-custom"></span>
                            <span style="color: #ddd;"><?php echo $serv['nome_servico']; ?></span>
                        </label>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="cliente-info" style="grid-column: span 2;">
                <label>Notas Adicionais</label>
                <input type="text" name="problema" placeholder="Observações extras...">
            </div>

            <div class="bloco-separado">
                <label>Estado Inicial</label>
                <select name="estado" style="margin-top: 5px;">
                    <option>Em diagnóstico</option><option>Em reparação</option><option>Concluído</option><option>Concluído e entregue</option>
                </select>
            </div>

            <div class="bloco-separado" style="display: flex; flex-direction: column; justify-content: center;">
                <label style="text-align: right;">Orçamento Total Estimado</label>
                <input type="hidden" name="preco_total" id="inputPreco" value="0.00">
                <h2 class="total-valor" id="totalHtml">0.00€</h2>
            </div>

            <div class="form-buttons" style="grid-column: span 2;">
                <button type="submit" name="submit" class="btn-save">
                    <span>💾</span> Guardar Reparação
                </button>
                <a href="../reparacoes.php" class="btn-cancel">Cancelar</a>
            </div>
        </div>
    </form>
</div>

<script>
function atualizar(el, id) {
    document.getElementById(id).classList.toggle('active', el.checked);
    let total = 0;
    document.querySelectorAll('input[type="checkbox"]:checked').forEach(i => total += parseFloat(i.getAttribute('data-preco')));
    document.getElementById('totalHtml').innerText = total.toFixed(2) + '€';
    document.getElementById('inputPreco').value = total.toFixed(2);
}
</script>
</body>
</html>
