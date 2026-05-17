<?php
session_start();

// 1. Caminho da ligação
include_once('base_de_dados/ligacao.php'); 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['username'];
$tipo_bd = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'usuario';

// --- 2. DICIONÁRIO DE NOMES ---
$nomes_tipos = [
    'admin'   => 'Admin',
    'tecnico' => 'Técnico(a)',
];
$tipo_exibicao = isset($nomes_tipos[$tipo_bd]) ? $nomes_tipos[$tipo_bd] : ucfirst($tipo_bd);

// --- 3. LÓGICA DE VERIFICAÇÃO DE PASSWORD TEMPORÁRIA ---
$deve_mudar_senha = false;
$sql_pass = "SELECT password FROM login WHERE username = ?";
$stmt_pass = $conn->prepare($sql_pass);
$stmt_pass->bind_param("s", $usuario);
$stmt_pass->execute();
$result_pass = $stmt_pass->get_result();

if ($row_pass = $result_pass->fetch_assoc()) {
    if (password_verify("12345", $row_pass['password'])) {
        $deve_mudar_senha = true;
    }
}

// --- 4. BUSCAR DADOS PARA OS WIDGETS (ESTATÍSTICAS REAIS) ---

// Contar Clientes
$res_clientes = mysqli_query($conn, "SELECT COUNT(*) as total FROM clientes");
$total_clientes = mysqli_fetch_assoc($res_clientes)['total'];

// Contar Staff (da tabela login ou staff, dependendo da tua estrutura)
$res_staff = mysqli_query($conn, "SELECT COUNT(*) as total FROM login");
$total_staff = mysqli_fetch_assoc($res_staff)['total'];

// Contar Reparações Concluídas
$res_concluidas = mysqli_query($conn, "SELECT COUNT(*) as total FROM reparacoes WHERE estado = 'Concluído'");
$total_concluidas = mysqli_fetch_assoc($res_concluidas)['total'];

// Contar Reparações Pendentes (Tudo o que não está concluído)
$res_pendentes = mysqli_query($conn, "SELECT COUNT(*) as total FROM reparacoes WHERE estado != 'Concluído'");
$total_pendentes = mysqli_fetch_assoc($res_pendentes)['total'];

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>RegisTech - Home</title>
    <link rel="stylesheet" href="css/home-style.css">
    <link rel="stylesheet" href="css/style-geral.css">
</head>
<body>

    <header class="header">
        <div class="header-left">
            <img onclick="window.location.href='home.php'" src="imagens_Login/logo.png" alt="Logo - RegisTech" class="logo">
            <h2>RegisTech - Home</h2>
        </div>

        <div class="header-right">
            <h3 class="bemvindo">Bem-vindo(a)</h3>
            <h2><?php echo $tipo_exibicao; ?> <?php echo htmlspecialchars($usuario); ?></h2>
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
            
            <?php if ($deve_mudar_senha): ?>
                <div style="background: linear-gradient(90deg, #ff4d4d, #b91c1c); color: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(255, 77, 77, 0.3);">
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem;">⚠️ Password Temporária Detetada!</h3>
                        <p style="margin: 5px 0 0 0; font-size: 0.9rem; opacity: 0.9;">Por favor, altere a sua password para garantir a segurança do sistema.</p>
                    </div>
                    <a href="base_de_dados/alterar_pass.php" style="background: white; color: #b91c1c; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.9rem; transition: 0.3s;">
                        Alterar Agora
                    </a>
                </div>
            <?php endif; ?>

            <h1 style="border-left: 5px solid #7b2cbf; padding-left: 15px; line-height: 1.1;">Visão Geral</h1>

            <div class="cards">
                <!-- Número de Clientes Real -->
                <div class="card" onclick="window.location.href='clientes.php'">
                    <p>Número de Clientes</p>
                    <h2><?php echo $total_clientes; ?></h2>
                </div>

                <!-- Reparações Concluídas Reais -->
                <div class="card" onclick="window.location.href='reparacoes.php'">
                    <p>Reparações concluídas</p>
                    <h2><?php echo $total_concluidas; ?></h2>
                </div>

                <!-- Número de Staff Real -->
                <div class="card" onclick="window.location.href='staff.php'">
                    <p>Número de Staff</p>
                    <h2><?php echo $total_staff; ?></h2>
                </div>

                <!-- Reparações Pendentes Reais -->
                <div class="card" onclick="window.location.href='reparacoes.php'">
                    <p>Reparações pendentes</p>
                    <h2 style="color: #ff4d4d;"><?php echo $total_pendentes; ?></h2>
                </div>
            </div>

<div class="recent-activity" style="margin-top: 40px;">
    <div class="table-container" style="background: #1a1a2e; border: 1px solid rgba(157, 78, 221, 0.15); border-radius: 16px; padding: 25px;">
        <h3 style="color: #9d4edd; margin-bottom: 20px; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 10px;">
            <span>🕒</span> Últimas Reparações Registadas
        </h3>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid rgba(123, 44, 191, 0.2);">
                    <th style="padding: 12px; color: #9d4edd; font-size: 0.85rem;">DATA</th>
                    <th style="padding: 12px; color: #9d4edd; font-size: 0.85rem;">EQUIPAMENTO</th>
                    <th style="padding: 12px; color: #9d4edd; font-size: 0.85rem;">ESTADO</th>
                    <th style="padding: 12px; color: #9d4edd; font-size: 0.85rem;">ID CLIENTE</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Busca as 5 reparações mais recentes
                $sql_recentes = "SELECT * FROM reparacoes ORDER BY id_reparacao DESC LIMIT 5";
                $res_recentes = mysqli_query($conn, $sql_recentes);

                if (mysqli_num_rows($res_recentes) > 0) {
                    while ($reg = mysqli_fetch_assoc($res_recentes)) {
                        // Formatação de cores para o estado
                        $cor_estado = ($reg['estado'] == 'Concluído') ? '#00ff7f' : '#e0aaff';
                        $bg_estado = ($reg['estado'] == 'Concluído') ? 'rgba(0, 255, 127, 0.1)' : 'rgba(157, 78, 221, 0.2)';
                ?>
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 15px; font-size: 0.9rem; color: #94a3b8;">
                            <?php echo date('d/m/Y', strtotime($reg['data_entrada'])); ?>
                        </td>
                        <td style="padding: 15px; font-weight: 500;">
                            <?php echo htmlspecialchars($reg['equipamento']); ?>
                        </td>
                        <td style="padding: 15px;">
                            <span style="background: <?php echo $bg_estado; ?>; color: <?php echo $cor_estado; ?>; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">
                                <?php echo htmlspecialchars($reg['estado']); ?>
                            </span>
                        </td>
                        <td style="padding: 15px; font-size: 0.9rem; color: #d8b4fe;">
                            #<?php echo $reg['id_cliente']; ?>
                        </td>
                    </tr>
                <?php 
                
                    } 
                } else {
                    echo "<tr><td colspan='4' style='padding: 20px; text-align: center; color: #64748b;'>Nenhuma atividade registada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
        </main>
    </div>

</body>
</html>