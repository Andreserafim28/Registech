<?php
session_start();
include_once('ligacao.php');

$username = $_POST['username'];
$password = $_POST['password'];

$username = mysqli_real_escape_string($conn, $username);

$sql = "SELECT * FROM login WHERE username='$username'";
$res = mysqli_query($conn, $sql);

if(mysqli_num_rows($res) == 1){
    $row = mysqli_fetch_assoc($res);
    $db_password = $row['password'];
    $login_ok = false;

    if(password_verify($password, $db_password)){
        $login_ok = true;
    }
    elseif($password === $db_password){
        $login_ok = true;
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE login SET password = ? WHERE id_user = ?");
        $update->bind_param("si", $newHash, $row['id_user']);
        $update->execute();
    }

if($login_ok){
        // 🚫 VERIFICAÇÃO DE ESTADO
        if($row['estado'] != 'ativo'){
            // Definir a mensagem personalizada conforme o estado
            $motivo = "A tua conta encontra-se atualmente inativa.";
            if($row['estado'] == 'ferias') $motivo = "Estás atualmente em período de férias.";
            if($row['estado'] == 'baixa') $motivo = "A tua conta está suspensa por motivo de baixa médica.";
            ?>
            <!DOCTYPE html>
            <html lang="pt">
            <head>
                <meta charset="UTF-8">
                <title>Acesso Restrito - RegisTech</title>
                <style>
                    :root { --roxo: #7b2cbf; --roxo-luz: #9d4edd; }
                    body { 
                        background-color: #0b0e14; 
                        color: white; 
                        font-family: 'Segoe UI', sans-serif; 
                        display: flex; 
                        justify-content: center; 
                        align-items: center; 
                        height: 100vh; 
                        margin: 0; 
                        overflow: hidden;
                        position: relative;
                    }

                    /* Logótipo Grande Transparente no Fundo */
                    body::before {
                        content: "REGISTECH";
                        position: absolute;
                        font-size: 15vw;
                        font-weight: 900;
                        color: rgba(157, 78, 221, 0.03); /* Muito transparente */
                        z-index: 0;
                        white-space: nowrap;
                        user-select: none;
                        pointer-events: none;
                    }

                    .lock-card {
                        background: rgba(22, 22, 37, 0.95);
                        padding: 50px;
                        border-radius: 24px;
                        border: 1px solid rgba(157, 78, 221, 0.2);
                        text-align: center;
                        max-width: 450px;
                        backdrop-filter: blur(10px);
                        box-shadow: 0 20px 50px rgba(0,0,0,0.6);
                        z-index: 1;
                        position: relative;
                    }

                    .icon-box {
                        width: 80px;
                        height: 80px;
                        background: rgba(123, 44, 191, 0.1);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 40px;
                        margin: 0 auto 25px;
                        color: var(--roxo-luz);
                        border: 1px solid rgba(157, 78, 221, 0.3);
                    }

                    h2 { margin: 0 0 10px 0; font-size: 1.8rem; letter-spacing: 1px; }
                    
                    .status-tag {
                        display: inline-block;
                        padding: 5px 15px;
                        background: rgba(255, 50, 50, 0.1);
                        color: #ff5555;
                        border-radius: 20px;
                        font-size: 0.8rem;
                        font-weight: bold;
                        text-transform: uppercase;
                        margin-bottom: 20px;
                        border: 1px solid rgba(255, 50, 50, 0.2);
                    }

                    p { color: #aaa; line-height: 1.6; margin-bottom: 30px; font-size: 1.05rem; }
                    
                    .btn-back {
                        display: block;
                        background: linear-gradient(135deg, #7b2cbf 0%, #9d4edd 100%);
                        color: white;
                        text-decoration: none;
                        padding: 15px;
                        border-radius: 12px;
                        font-weight: bold;
                        transition: 0.3s;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                    }
                    
                    .btn-back:hover { 
                        transform: translateY(-3px); 
                        box-shadow: 0 8px 20px rgba(123, 44, 191, 0.4);
                        filter: brightness(1.1);
                    }
                </style>
            </head>
            <body>
                <div class="lock-card">
                    <div class="icon-box">🔒</div>
                    <div class="status-tag">Acesso Suspenso</div>
                    <h2>Olá, <?php echo htmlspecialchars($row['username']); ?></h2>
                    <p><?php echo $motivo; ?><br><br>Por motivos de segurança e gestão, o acesso ao painel técnico está bloqueado até ao teu regresso.</p>
                    <p style="font-size: 15px;">Se isto for um erro, por favor, contacte o suporte técnico.</p>
                    <a href="../login.php" class="btn-back">Voltar ao Início</a>
                </div>
            </body>
            </html>
            <?php
            exit();
        }

        $_SESSION['username'] = $row['username'];
        $_SESSION['tipo'] = $row['tipo'];
        header("Location: ../home.php");
        exit();

    } else {
        header("Location: ../login.php?erro=1");
        exit();
    }
} else {
    header("Location: ../login.php?erro=2");
    exit();
}
?>
