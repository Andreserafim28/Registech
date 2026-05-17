<?php
session_start();
include_once('base_de_dados/ligacao.php');

/* 🔐 PROTEÇÃO APENAS PARA ADMIN */
if (!isset($_SESSION['username']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$usuario_logado = $_SESSION['username'];
$tipo_bd = $_SESSION['tipo'];
$nomes_tipos = ['admin' => 'Admin', 'tecnico' => 'Técnico', 'staff' => 'Staff'];
$tipo_logado_display = isset($nomes_tipos[$tipo_bd]) ? $nomes_tipos[$tipo_bd] : ucfirst($tipo_bd);

/* =========================================
    👤 LÓGICA: UTILIZADORES
========================================= */
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo']; 
    mysqli_query($conn, "INSERT INTO login (username, password, tipo, estado) VALUES ('$username', '$password', '$tipo', 'ativo')");
    header("Location: admin_panel.php?tab=users&success=1");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM login WHERE id_user = $id");
    header("Location: admin_panel.php?tab=users&deleted=1");
    exit();
}

if (isset($_GET['reset_id'])) {
    $id = intval($_GET['reset_id']);
    $new_pass = password_hash("12345", PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE login SET password = '$new_pass' WHERE id_user = $id");
    header("Location: admin_panel.php?tab=users&reset=1");
    exit();
}

/* =========================================
    📄 LÓGICA: PREÇÁRIO
========================================= */
if (isset($_POST['add_servico'])) {
    $nome = mysqli_real_escape_string($conn, $_POST['nome_servico']);
    $cat = mysqli_real_escape_string($conn, $_POST['categoria']);
    $preco = $_POST['preco'];
    $prazo = mysqli_real_escape_string($conn, $_POST['prazo']);
    mysqli_query($conn, "INSERT INTO servicos (nome_servico, categoria, preco, prazo_medio) VALUES ('$nome', '$cat', '$preco', '$prazo')");
    header("Location: admin_panel.php?tab=precario&precario_ok=1");
    exit();
}

if (isset($_GET['delete_servico'])) {
    $id = intval($_GET['delete_servico']);
    mysqli_query($conn, "DELETE FROM servicos WHERE id_servico = $id");
    header("Location: admin_panel.php?tab=precario&precario_del=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - RegisTech</title>
    <link rel="stylesheet" href="css/home-style.css">
    <link rel="stylesheet" href="css/style-geral.css">
    <link rel="stylesheet" href="css/style-geral.css?v=1.1">
    <style>
        .header { position: sticky; top: 0; z-index: 1000; background: #0b0b15; border-bottom: 1px solid #2a2a3d; }
        a { text-decoration: none !important; }
        
        /* Mensagens de Feedback */
        .msg-alerta {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .msg-sucesso { background: rgba(0, 255, 136, 0.1); color: #00ff88; border: 1px solid #00ff88; }
        .msg-erro { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; }
        .msg-info { background: rgba(224, 170, 255, 0.1); color: #e0aaff; border: 1px solid #e0aaff; }

        .tabs-header { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 1px solid #2a2a3d; }
        .tab-btn { background: none; border: none; color: #888; padding: 12px 20px; cursor: pointer; font-weight: bold; }
        .tab-btn.active { color: #9d4edd; border-bottom: 2px solid #9d4edd; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .admin-card { background: #161625; padding: 20px; border-radius: 12px; border: 1px solid #9d4edd; margin-bottom: 25px; }
        .form-inline { display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
        .form-group { flex: 1; display: flex; flex-direction: column; gap: 5px; }
        .form-group input, .form-group select { padding: 10px; border-radius: 6px; border: none; background: #2a2a3d; color: white; }
        .btn-add { background: #9d4edd; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<header class="header">
    <div class="header-left">
        <img onclick="window.location.href='home.php'" src="imagens_login/logo.png" alt="Logo" class="logo">
        <h2>RegisTech - Gestão Administrativa</h2>
    </div>
    <div class="header-right">
        <h3 class="bemvindo">Bem-vindo(a)</h3>
        <h2><?php echo $tipo_logado_display; ?> <?php echo htmlspecialchars($usuario_logado); ?></h2>
    </div>
</header>

<div class="layout">
    <aside class="sidebar">
        <h2>MENU</h2>
        <ul>
            <li><a class="action" href="home.php"><span>🏠</span> Home</a></li>
            <li><a class="action" href="clientes.php"><span>👥</span> Clientes</a></li>
            <li><a class="action" href="reparacoes.php"><span>🔧</span> Reparações</a></li>
            <li><a class="action" href="staff.php"><span>🧑‍💼</span> Colaboradores</a></li>
            <li><a class="action" href="meus_pedidos.php"><span>📥</span> Meus Pedidos</a></li>
            <li><a class="action" href="precario.php"><span>📄</span> Preçário</a></li>
            <li class="active"><a class="action" href="admin_panel.php"><span>⚙️</span> Admin Panel</a></li>
            <li class="logout"><a class="action" href="base_de_dados/logout.php"><span>🚪</span> Sair</a></li>
        </ul>
    </aside>

    <main class="content">
        <h1>Painel de Controlo</h1>

        <?php if(isset($_GET['success'])): ?>
            <div class="msg-alerta msg-sucesso">✅ Novo utilizador registado com sucesso!</div>
        <?php endif; ?>

        <?php if(isset($_GET['deleted'])): ?>
            <div class="msg-alerta msg-erro">🗑️ Utilizador removido do sistema.</div>
        <?php endif; ?>

        <?php if(isset($_GET['reset'])): ?>
            <div class="msg-alerta msg-info">🔄 Password redefinida para o padrão: 12345</div>
        <?php endif; ?>

        <?php if(isset($_GET['precario_ok'])): ?>
            <div class="msg-alerta msg-sucesso">✅ Serviço adicionado ao preçário!</div>
        <?php endif; ?>

        <?php if(isset($_GET['precario_del'])): ?>
            <div class="msg-alerta msg-erro">🗑️ Item removido do preçário.</div>
        <?php endif; ?>

        <div class="tabs-header">
            <button class="tab-btn <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'users') ? 'active' : ''; ?>" onclick="openTab(event, 'users')">👤 Utilizadores</button>
            <button class="tab-btn <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'precario') ? 'active' : ''; ?>" onclick="openTab(event, 'precario')">📄 Preçário</button>
        </div>

        <div id="users" class="tab-content <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'users') ? 'active' : ''; ?>">
            <div class="admin-card">
                <form method="POST" class="form-inline">
                    <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                    <div class="form-group"><label>Cargo</label>
                        <select name="tipo"><option value="tecnico">Técnico</option><option value="admin">Administrador</option></select>
                    </div>
                    <button type="submit" name="add_user" class="btn-add">Registar</button>
                </form>
            </div>
            <div class="table-container">
                <table class="custom-table">
                    <thead><tr><th>ID</th><th>Username</th><th>Cargo</th><th>Estado</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php
                        $res_u = mysqli_query($conn, "SELECT * FROM login");
                        while ($u = mysqli_fetch_assoc($res_u)) {
                            $cargo = isset($nomes_tipos[$u['tipo']]) ? $nomes_tipos[$u['tipo']] : ucfirst($u['tipo']);
                        ?>
                        <tr>
                            <td>#<?php echo $u['id_user']; ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo $cargo; ?></td>
                            <td><span style="color:<?php echo ($u['estado']=='ativo'?'#00ff88':'#ff4d4d'); ?>"><?php echo ucfirst($u['estado']); ?></span></td>
                            <td>
                                <a href="admin_panel.php?tab=users&delete=<?php echo $u['id_user']; ?>" onclick="return confirm('Apagar utilizador?');">🗑️</a>
                                <a href="admin_panel.php?tab=users&reset_id=<?php echo $u['id_user']; ?>" style="margin-left:10px;">🔄</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="precario" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'precario') ? 'active' : ''; ?>">
            <div class="admin-card">
                <form method="POST" class="form-inline">
                    <div class="form-group"><label>Serviço</label><input type="text" name="nome_servico" required></div>
                    <div class="form-group"><label>Categoria</label>
                        <select name="categoria"><option>Hardware</option><option>Software</option><option>Telemóveis</option><option>Redes</option></select>
                    </div>
                    <div class="form-group"><label>Preço (€)</label><input type="number" step="0.01" name="preco" required></div>
                    <div class="form-group"><label>Prazo</label><input type="text" name="prazo" placeholder="Ex: 24h"></div>
                    <button type="submit" name="add_servico" class="btn-add">Adicionar</button>
                </form>
            </div>
            <div class="table-container">
                <table class="custom-table">
                    <thead><tr><th>Serviço</th><th>Categoria</th><th>Preço</th><th>Prazo</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php
                        $res_p = mysqli_query($conn, "SELECT * FROM servicos ORDER BY categoria ASC");
                        while ($s = mysqli_fetch_assoc($res_p)) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['nome_servico']); ?></td>
                            <td><?php echo $s['categoria']; ?></td>
                            <td style="color:#00ff88; font-weight:bold;"><?php echo number_format($s['preco'], 2); ?>€</td>
                            <td><?php echo $s['prazo_medio']; ?></td>
                            <td><a href="admin_panel.php?tab=precario&delete_servico=<?php echo $s['id_servico']; ?>" onclick="return confirm('Remover item?');">🗑️</a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    // Função para as abas
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // NOVO: Script para fazer as mensagens desaparecerem
    window.onload = function() {
        const alerts = document.querySelectorAll('.msg-alerta');
        if (alerts.length > 0) {
            setTimeout(() => {
                alerts.forEach(alert => {
                    alert.classList.add('hidden'); // Começa a desaparecer (fade)
                    setTimeout(() => alert.remove(), 600); // Remove do HTML após a animação
                });
            }, 4000); // 4 segundos visível antes de começar a sumir
        }
    };
</script>
</body>
</html>
