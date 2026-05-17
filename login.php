
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>RegisTech - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/login-style.css">
    <link rel="stylesheet" href="css/style-geral.css">
    
    <style>
        /* Estilo para o Alerta de Erro - Mantendo o teu padrão roxo/escuro */
        .alert-erro {
            background: rgba(255, 85, 85, 0.1);
            border: 1px solid rgba(255, 85, 85, 0.4);
            color: #ff5555;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            text-align: center;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group { position: relative; display: flex; align-items: center; margin-bottom: 15px; }
        .toggle-password { position: absolute; right: 15px; cursor: pointer; color: #9d4edd; z-index: 10; }
        input[type="password"], input[type="text"] { padding-right: 45px !important; }
        
        /* Garantir que o body não tem margens estranhas que causem a barra branca */
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>

    <div class="background"></div>

    <header class="top-bar">
        <div class="logo">
            <img src="imagens_Login/logo.png" alt="RegisTech Logo" width="85" height="100">
            <h1 style="font-size: 30px; margin: 0;">RegisTech</h1>
        </div>
    </header>

    <div class="login-container">
        <div class="login-card">
            <h1>Login</h1>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alert-erro">
                    <?php 
                        if($_GET['erro'] == 1) echo "❌ Password incorreta.";
                        if($_GET['erro'] == 2) echo "❌ Utilizador não encontrado.";
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="base_de_dados/verificacao.php">
                <div class="input-group">
                    <span class="icon">👤</span>
                    <input type="text" name="username" placeholder="Digite o seu usuario" required>
                </div>

                <div class="input-group">
                    <span class="icon">🔒</span>
                    <input type="password" name="password" id="password" placeholder="Digite a sua password" required>
                    <i class="far fa-eye toggle-password" id="toggleEye"></i>
                </div>

                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <script>
        const toggleEye = document.querySelector('#toggleEye');
        const password = document.querySelector('#password');
        toggleEye.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>