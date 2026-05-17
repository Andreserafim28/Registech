<?php
session_start();
include_once('base_de_dados/ligacao.php');

// Procura os serviços na base de dados
$sql = "SELECT * FROM servicos ORDER BY categoria, nome_servico";
$res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RegisTech - Preçário de Serviços</title>
    <!-- CSS Próprio -->
     <link rel="stylesheet" href="css/style-geral.css">
    <link rel="stylesheet" href="css/precario-style.css">
    <!-- FontAwesome para os ícones (Relógio, etc) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="container-precario">
    
    <div class="header-precario">
        <img onclick="window.location.href='home.php'" src="imagens_Login/logo.png" alt="Logo RegisTech">
        <h1>Tabela de Preços e Serviços</h1>
        <p>Serviços técnicos especializados com garantia RegisTech</p>
    </div>

    <div class="grid-servicos">
        <?php 
        if(mysqli_num_rows($res) > 0) {
            while($servico = mysqli_fetch_assoc($res)): 
        ?>
            <div class="card-servico">
                <div class="categoria-tag"><?php echo htmlspecialchars($servico['categoria']); ?></div>
                
                <div class="info-principal">
                    <h3><?php echo htmlspecialchars($servico['nome_servico']); ?></h3>
                    <div class="entrega">
                        <i class="fas fa-clock"></i> 
                        <span>Prazo: <?php echo htmlspecialchars($servico['prazo_medio']); ?></span>
                    </div>
                </div>

                <div class="preco-tag">
                    <?php echo number_format($servico['preco'], 0, ',', ''); ?>€
                </div>
            </div>
        <?php 
            endwhile; 
        } else {
            echo "<p style='text-align:center; grid-column: span 3;'>Nenhum serviço encontrado na base de dados.</p>";
        }
        ?>
    </div>

    <div class="footer-precario">
        <a href="home.php" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar ao Início
        </a>
        <p>* Os preços apresentados são base e podem variar conforme a complexidade ou custo de peças.</p>
    </div>

</div>

</body>
</html>