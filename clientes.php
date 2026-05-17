<?php
session_start();

// 1. Verifica se o utilizador está logado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 2. Ligação à Base de Dados
include_once('base_de_dados/ligacao.php'); 

$usuario = $_SESSION['username'];
$tipo_bd = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'staff';

// --- 3. DICIONÁRIO DE NOMES (Para exibição bonita) ---
$nomes_tipos = [
    'admin'   => 'Admin',
    'tecnico' => 'Técnico',
    'staff'   => 'Staff'
];
$tipo_exibicao = isset($nomes_tipos[$tipo_bd]) ? $nomes_tipos[$tipo_bd] : ucfirst($tipo_bd);

// --- 4. LÓGICA DE PESQUISA ---
$pesquisa = "";
if (isset($_GET['search'])) {
    $pesquisa = mysqli_real_escape_string($conn, $_GET['search']);
}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RegisTech - Clientes</title>
    <link rel="stylesheet" href="css/clientes-style.css">
    <link rel="stylesheet" href="css/home-style.css">
    <link rel="stylesheet" href="css/style-geral.css">
    <style>
        /* Estilo extra para a barra de pesquisa brilhar no foco */
        .search-container:focus-within {
            border-color: #7b2cbf !important;
            box-shadow: 0 0 10px rgba(123, 44, 191, 0.5);
            transition: 0.3s;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="header-left">
            <img onclick="window.location.href='home.php'" src="imagens_login/logo.png" alt="Logo - RegisTech" class="logo" style="cursor:pointer;">
            <h2>RegisTech - Clientes</h2>
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
            <div class="content-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h1>Gestão de Clientes</h1>
                
                <div class="header-actions" style="display: flex; gap: 15px; align-items: center;">
                    <form action="clientes.php" method="GET" class="search-container" style="display: flex; background: rgba(255,255,255,0.05); border-radius: 8px; padding: 5px 15px; border: 1px solid rgba(157, 78, 221, 0.3); align-items: center;">
                        <input type="text" name="search" placeholder="Procurar cliente..." 
                               value="<?php echo htmlspecialchars($pesquisa); ?>"
                               style="background: none; border: none; color: white; outline: none; padding: 8px; width: 220px; font-size: 0.9rem;">
                        <button type="submit" style="background: none; border: none; cursor: pointer; color: #9d4edd; font-size: 1.1rem;">🔍</button>
                        
                        <?php if($pesquisa != ""): ?>
                            <a href="clientes.php" title="Limpar pesquisa" style="text-decoration: none; color: #ff4d4d; margin-left: 10px; font-weight: bold; font-size: 1.1rem;">×</a>
                        <?php endif; ?>
                    </form>

                    <a href="base_de_dados/adicionar_clientes.php" class="btn-add">
                        <span class="icon-plus">+</span> Novo Cliente
                    </a>
                </div>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nº Cliente</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Morada</th>
                            <th>Data Registo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // CONSTRUÇÃO DA QUERY DINÂMICA
                        $sql = "SELECT * FROM clientes";
                        
                        if ($pesquisa != "") {
                            // Procura por ID, Nome, Email ou Telefone
                            $sql .= " WHERE nome LIKE '%$pesquisa%' 
                                     OR email LIKE '%$pesquisa%' 
                                     OR telefone LIKE '%$pesquisa%' 
                                     OR id_cliente = '$pesquisa'";
                        }

                        $sql .= " ORDER BY id_cliente DESC";
                        
                        $res = mysqli_query($conn, $sql);

                        if ($res && mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                $data = date('d/m/Y', strtotime($row['data_registo']));
                                ?>
                                <tr>
                                    <td><?php echo $row['id_cliente']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nome']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['telefone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['morada']); ?></td>
                                    <td><?php echo $data; ?></td>
                                    <td>
                                        <div class='actions-btns'>
                                            <a href='base_de_dados/ver_cliente.php?id=<?php echo $row['id_cliente']; ?>' class='btn-view' title='Ver'>👁️</a>
                                            <a href='base_de_dados/editar_clientes.php?id=<?php echo $row['id_cliente']; ?>' class='btn-edit' title='Editar'>✏️</a>
                                            <a href='base_de_dados/eleminar_cliente.php?id=<?php echo $row['id_cliente']; ?>' class='btn-delete' title='Eliminar' onclick="return confirm('Tem a certeza que deseja eliminar este cliente?')">🗑️</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align:center; padding: 50px; color: #9d4edd;'>";
                            echo ($pesquisa != "") ? "Nenhum cliente encontrado para: <strong>" . htmlspecialchars($pesquisa) . "</strong>" : "Nenhum cliente registado.";
                            echo "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>                   
</body>
</html>
