<?php
session_start();
include_once('base_de_dados/ligacao.php');

/* 🔐 PROTEÇÃO LOGIN */
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// DEFINIÇÃO DAS VARIÁVEIS PARA A SIDEBAR E CABEÇALHO
$usuario_logado = $_SESSION['username'];
$tipo_bd = $_SESSION['tipo'] ?? 'staff'; // Variável corrigida aqui

/* --- 1. DICIONÁRIO DE NOMES (Para exibição bonita) --- */
$nomes_tipos = [
    'admin'   => 'Admin',
    'tecnico' => 'Técnico',
    'staff'   => 'Staff'
];

$tipo_logado_display = isset($nomes_tipos[$tipo_bd]) ? $nomes_tipos[$tipo_bd] : ucfirst($tipo_bd);

/* =========================
    ➕ ADICIONAR UTILIZADOR
========================= */
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo']; 
    $estado = 'ativo';

    $stmt = $conn->prepare("INSERT INTO login (username, password, tipo, estado) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $username, $password, $tipo, $estado);
        $stmt->execute();
    }
    header("Location: admin_panel.php?success=1");
    exit();
}

/* =========================
    ❌ ELIMINAR UTILIZADOR
========================= */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM login WHERE id_user = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_panel.php?deleted=1");
    exit();
}

/* =========================
    🔄 RESET PASSWORD
========================= */
if (isset($_GET['reset_id'])) {
    $id = $_GET['reset_id'];
    $new_password = password_hash("12345", PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE login SET password = ? WHERE id_user = ?");
    $stmt->bind_param("si", $new_password, $id);
    $stmt->execute();
    header("Location: admin_panel.php?reset=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Admin Panel - RegisTech</title>
<link rel="stylesheet" href="css/home-style.css">
<link rel="stylesheet" href="css/style-geral.css">

<style>
.admin-form-wrapper {
    display:flex;
    justify-content:center;
    margin-bottom:30px;
}

.admin-form-container {
    background:#1e1e2f;
    padding:25px;
    border-radius:12px;
    width:100%;
    max-width:500px;
    border:1px solid #9d4edd;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.admin-title {
    text-align:center;
    color:#9d4edd;
    margin-bottom:15px;
}

.admin-form {
    display:flex;
    flex-direction:column;
    gap:10px;
}

.admin-form input,
.admin-form select {
    padding:12px;
    border-radius:8px;
    border:none;
    background:#2a2a3d;
    color:white;
}

.admin-form button {
    padding:12px;
    background:#9d4edd;
    border:none;
    border-radius:8px;
    color:white;
    cursor:pointer;
    font-weight:bold;
    transition: 0.3s;
}

.admin-form button:hover {
    background:#7b2cbf;
}

.success-msg {color:#00ff88; text-align:center; font-weight:bold; margin-bottom: 10px;}
</style>
</head>
<body>

<header class="header">
    <div class="header-left">
        <img onclick="window.location.href='home.php'" src="/imagens_login/logo.png" alt="Logo - RegisTech" class="logo">
        <h2>RegisTech - Admin Panel</h2>
    </div>

    <div class="header-right">
        <h3 class="bemvindo">Bem-vindo(a)</h3>
        <!-- CORREÇÃO: Nome do tipo agora aparece bonito -->
        <h2><?php echo $tipo_logado_display; ?> <?php echo htmlspecialchars($usuario_logado); ?></h2>
    </div>
</header>

<div class="layout">

        <aside class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'home.php') ? 'active' : ''; ?>">
                    <a class="action" href="home.php"><span>🏠</span> Home</a>
                </li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'clientes.php') ? 'active' : ''; ?>">
                    <a class="action" href="clientes.php"><span>👥</span> Clientes</a>
                </li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'reparacoes.php') ? 'active' : ''; ?>">
                    <a class="action" href="reparacoes.php"><span>🔧</span> Reparações</a>
                </li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'staff.php') ? 'active' : ''; ?>">
                    <a class="action" href="staff.php"><span>🧑‍💼</span> Colaboradores</a>
                </li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'meus_pedidos.php') ? 'active' : ''; ?>">
                    <a class="action" href="meus_pedidos.php"><span>📥</span> Meus Pedidos</a>
                </li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'precario.php') ? 'active' : ''; ?>">
                    <a class="action" href="precario.php"><span>📄</span> Preçário</a>
                </li>
                <?php if ($tipo_bd === 'admin') { ?>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin_panel.php') ? 'active' : ''; ?>">
                        <a class="action" href="admin_panel.php"><span>⚙️</span> Admin Panel</a>
                    </li>
                <?php } ?>
                <li class="logout">
                    <a class="action" href="base_de_dados/logout.php"><span>🚪</span> Sair</a>
                </li>
            </ul>
        </aside>

<main class="content">

<h1>Gestão Administrativo</h1>

<!-- MENSAGENS -->
<?php if(isset($_GET['success'])) echo "<p class='success-msg'>✅ Utilizador criado com sucesso!</p>"; ?>
<?php if(isset($_GET['deleted'])) echo "<p class='success-msg' style='color:#ff4d4d;'>❌ Utilizador removido!</p>"; ?>
<?php if(isset($_GET['reset'])) echo "<p class='success-msg' style='color:#e0aaff;'>🔄 Password redefinida para: 12345</p>"; ?>


<!-- FORMULÁRIO -->
<div class="admin-form-wrapper">
    <div class="admin-form-container">
        <h3 class="admin-title">Adicionar Colaboradores</h3>
        <form method="POST" class="admin-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="tipo">
                <option value="tecnico">Técnico</option>
                <option value="admin">Administrador</option>
            </select>
            <button type="submit" name="add_user">Confirmar Registo</button>
        </form>
    </div>
</div>


<!-- TABELA -->
<div class="table-container">
    <h3 style="color:#9d4edd; margin-bottom:15px;">Utilizadores Registados</h3>
    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Cargo</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM login");
            while ($row = $result->fetch_assoc()) {
                // Traduzir o cargo da linha da tabela para o nome bonito
                $cargo_bd = $row['tipo'];
                $cargo_display = isset($nomes_tipos[$cargo_bd]) ? $nomes_tipos[$cargo_bd] : ucfirst($cargo_bd);
            ?>
            <tr>
                <td><?php echo $row['id_user']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <!-- CORREÇÃO: Mostra "Técnico" em vez de "tecnico" -->
                <td><?php echo $cargo_display; ?></td>
                <td>
                    <span style="color: <?php echo ($row['estado'] == 'ativo') ? '#00ff88' : '#ff4d4d'; ?>;">
                        <?php echo ucfirst($row['estado']); ?>
                    </span>
                </td>
                <td>
                    <a href="admin_panel.php?delete=<?php echo $row['id_user']; ?>"
                       onclick="return confirm('Deseja eliminar este utilizador?');"
                       title="Eliminar" style="text-decoration:none;">🗑️</a>

                    <a href="admin_panel.php?reset_id=<?php echo $row['id_user']; ?>"
                       onclick="return confirm('Deseja repor a password para 12345?');"
                       title="Reset Password" style="text-decoration:none; margin-left:15px;">🔄</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</main>
</div>

</body>
</html>
