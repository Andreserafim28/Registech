<?php
session_start();
include_once('base_de_dados/ligacao.php');

// 1. Verifica se o utilizador está logado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$usuario_logado = $_SESSION['username'];
$tipo_bd = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'staff';

// --- 2. DICIONÁRIO DE NOMES (Para exibição bonita) ---
$nomes_tipos = [
    'admin'   => 'Admin',
    'tecnico' => 'Técnico',
    'staff'   => 'Staff'
];
$tipo_exibicao = isset($nomes_tipos[$tipo_bd]) ? $nomes_tipos[$tipo_bd] : ucfirst($tipo_bd);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RegisTech - Colaboradores</title>
    <link rel="stylesheet" href="css/reparacoes-style.css">
    <link rel="stylesheet" href="css/home-style.css">
    <link rel="stylesheet" href="css/style-geral.css">
    <style>
        /* Cores para os estados do Staff */
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: capitalize;
        }
        .ativo { background: rgba(0, 255, 136, 0.15); color: #00ff88; }
        .ferias { background: rgba(255, 188, 66, 0.15); color: #ffbc42; }
        .baixa { background: rgba(255, 77, 77, 0.15); color: #ff4d4d; }
        
        .actions-btns a {
            text-decoration: none;
            margin: 0 5px;
            font-size: 1.2rem;
            transition: transform 0.2s;
            display: inline-block;
        }
        .actions-btns a:hover { transform: scale(1.2); }
    </style>
</head>

<body>

<header class="header">
    <div class="header-left">
        <img onclick="window.location.href='home.php'" src="imagens_login/logo.png" class="logo">
        <h2>RegisTech - Colaboradores</h2>
    </div>

    <div class="header-right">
        <h3 class="bemvindo">Bem-vindo(a)</h3>
        <!-- CORREÇÃO: Nome do tipo com acento -->
        <h2><?php echo $tipo_exibicao; ?> <?php echo htmlspecialchars($usuario_logado); ?></h2>
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
        <div class="content-header">
            <h1>Equipa RegisTech</h1>
        </div>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Cargo</th>
                        <th>Estado Atual</th>
                        <th>Gestão de Estado</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                $sql = "SELECT * FROM login ORDER BY id_user DESC";
                $res = mysqli_query($conn, $sql);

                if(!$res){
                    die("Erro SQL: " . mysqli_error($conn));
                }

                if(mysqli_num_rows($res) > 0){
                    while($row = mysqli_fetch_assoc($res)){
                        // Traduzir o cargo para a tabela
                        $cargo_linha = isset($nomes_tipos[$row['tipo']]) ? $nomes_tipos[$row['tipo']] : ucfirst($row['tipo']);
                        $estado_classe = strtolower($row['estado']);
                        ?>
                        <tr>
                            <td><?php echo $row['id_user']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo $cargo_linha; ?></td>
                            <td>
                                <span class="status-badge <?php echo $estado_classe; ?>">
                                    <?php echo htmlspecialchars($row['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($tipo_bd === 'admin'){ ?>
                                    <div class='actions-btns'>
                                        <a href='base_de_dados/mudar_estado.php?id=<?php echo $row['id_user']; ?>&estado=ativo' title='Marcar como Ativo'>🟢</a>
                                        <a href='base_de_dados/mudar_estado.php?id=<?php echo $row['id_user']; ?>&estado=ferias' title='Marcar Férias'>🌴</a>
                                        <a href='base_de_dados/mudar_estado.php?id=<?php echo $row['id_user']; ?>&estado=baixa' title='Marcar Baixa Médica'>🤒</a>
                                    </div>
                                <?php } else { ?>
                                    <span style="font-size: 0.8rem; color: #666;">Apenas Administradores</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;padding:30px;'>Nenhum utilizador registado.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
