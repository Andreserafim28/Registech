<?php
session_start();

// 1. Verificar se o utilizador está logado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 2. Incluir a ligação à base de dados
// Ajustei para o caminho relativo mais comum. Verifica se a pasta se chama 'base_de_dados'
// Se os dois ficheiros estão na pasta 'base_de_dados', usa apenas isto:
include_once('ligacao.php');

if (!isset($conn)) {
    die("Erro: A variável de conexão \$conn não foi definida. Verifica o ficheiro ligacao.php.");
}

$usuario = $_SESSION['username'];
$mensagem = "";

// 3. Lógica de Alteração
if (isset($_POST['btn_alterar'])) {
    $pass_atual = $_POST['pass_atual'];
    $nova_pass = $_POST['nova_pass'];
    $confirma_pass = $_POST['confirma_pass'];

    // Buscar a password atual (hash) na BD para validar
    $stmt = $conn->prepare("SELECT password FROM login WHERE username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Validações
        if (!password_verify($pass_atual, $row['password'])) {
            $mensagem = "<div style='color:#ff4d4d; margin-bottom:15px;'>❌ A password atual está incorreta!</div>";
        } elseif ($nova_pass !== $confirma_pass) {
            $mensagem = "<div style='color:#ff4d4d; margin-bottom:15px;'>❌ As novas passwords não coincidem!</div>";
        } elseif (strlen($nova_pass) < 4) {
            $mensagem = "<div style='color:#ffbc42; margin-bottom:15px;'>⚠️ A nova password é demasiado curta (mínimo 4 caracteres).</div>";
        } else {
            // Tudo OK -> Gerar novo Hash
            $novo_hash = password_hash($nova_pass, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE login SET password = ? WHERE username = ?");
            $update->bind_param("ss", $novo_hash, $usuario);
            
if ($update->execute()) {
    $mensagem = "<div style='color:#00ff88; margin-bottom:15px;'>✅ Sucesso! A encerrar sessão para validar...</div>";
    
    // Como já estás dentro da pasta 'base_de_dados', basta chamar o logout.php diretamente
    header("refresh:2;url=logout.php");
    exit();
}else {
                $mensagem = "<div style='color:#ff4d4d; margin-bottom:15px;'>❌ Erro ao atualizar na base de dados.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RegisTech - Alterar Password</title>
    <link rel="stylesheet" href="css/home-style.css">
    <style>
        body { 
            background-color: #0b0b14; 
            color: white; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0;
        }
        .container-central {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box-alterar {
            background: #13131f;
            padding: 40px;
            border-radius: 15px;
            border: 1px solid rgba(157, 78, 221, 0.3);
            width: 100%;
            max-width: 380px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .box-alterar h2 { 
            color: #9d4edd; 
            margin-bottom: 25px; 
            font-size: 1.8rem;
            letter-spacing: 1px;
        }
        .form-grupo { margin-bottom: 20px; text-align: left; }
        .form-grupo label { 
            display: block; 
            color: #a0a0b0; 
            margin-bottom: 8px; 
            font-size: 0.9rem; 
        }
        .form-grupo input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #2d2d44;
            background: #0b0b14;
            color: white;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }
        .form-grupo input:focus {
            border-color: #9d4edd;
            box-shadow: 0 0 8px rgba(157, 78, 221, 0.2);
        }
        .btn-update {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #9d4edd, #7b2cbf);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-update:hover { 
            filter: brightness(1.2);
            transform: translateY(-2px);
        }
        .voltar { 
            display: inline-block; 
            margin-top: 20px; 
            color: #666; 
            text-decoration: none; 
            font-size: 0.85rem; 
            transition: 0.3s;
        }
        .voltar:hover { color: #9d4edd; }
    </style>
</head>
<body>
    <div class="container-central">
        <div class="box-alterar">
            <h2>Segurança</h2>
            
            <?php echo $mensagem; ?>

            <form method="POST" action="">
                <div class="form-grupo">
                    <label>Password Atual</label>
                    <input type="text" name="pass_atual" placeholder="Sua senha atual" required>
                </div>
                <div class="form-grupo">
                    <label>Nova Password</label>
                    <input type="password" name="nova_pass" placeholder="Mínimo 4 caracteres" required>
                </div>
                <div class="form-grupo">
                    <label>Confirmar Nova Password</label>
                    <input type="password" name="confirma_pass" placeholder="Repita a nova senha" required>
                </div>
                <button type="submit" name="btn_alterar" class="btn-update">Guardar Alterações</button>
            </form>

            <a href="../home.php" class="voltar">← Voltar para a Dashboard</a>
        </div>
    </div>
</body>
</html>