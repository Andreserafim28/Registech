<?php
session_start();
include_once('base_de_dados/ligacao.php'); 

// 1. Verifica se o utilizador está logado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['username'];
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
    <title>RegisTech - Reparações</title>
    <link rel="stylesheet" href="css/reparacoes-style.css">
    <link rel="stylesheet" href="css/home-style.css">
    <link rel="stylesheet" href="css/style-geral.css">
    <style>
        /* Estilos rápidos para as tags de estado */
        .status-badge {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pendente { background: rgba(255, 188, 66, 0.2); color: #ffbc42; }
        .status-concluido { background: rgba(0, 255, 136, 0.2); color: #00ff88; }
        .status-entregue { background: rgba(157, 78, 221, 0.2); color: #e0aaff; }
    </style>
</head>
<body>

    <header class="header">
        <div class="header-left">
            <img onclick="window.location.href='home.php'" src="imagens_Login/logo.png" class="logo">
            <h2>RegisTech - Reparações</h2>
        </div>

        <div class="header-right">
            <h3 class="bemvindo">Bem-vindo(a)</h3>
            <!-- CORREÇÃO: Nome do tipo com acento -->
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
            <div class="content-header">
                <h1>Gestão de Reparações</h1>
                <a href="base_de_dados/adicionar_reparacao.php" class="btn-add">
                    + Nova Reparação
                </a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Equipamento</th>
                            <th>Problema</th>
                            <th>Estado</th>
                            <th>Data Entrada</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                    // SQL melhorado para tentar mostrar o nome do cliente em vez de apenas o ID (Join)
                    // Se a tua tabela de clientes se chamar 'clientes' e tiver a coluna 'nome':
                    $sql = "SELECT r.*, c.nome as nome_cliente 
                            FROM reparacoes r 
                            LEFT JOIN clientes c ON r.id_cliente = c.id_cliente 
                            ORDER BY r.id_reparacao DESC";

                    $res = mysqli_query($conn, $sql);

                    if(!$res){
                        die("Erro SQL: " . mysqli_error($conn));
                    }

                    if(mysqli_num_rows($res) > 0){
                        while($row = mysqli_fetch_assoc($res)){
                            $data = date('d/m/Y', strtotime($row['data_entrada']));
                            
                            // Lógica de cores para o estado
                            $classe_status = "status-pendente";
                            $estado_texto = $row['estado'];

                            if(strtolower($estado_texto) == 'concluido' || strtolower($estado_texto) == 'concluído') $classe_status = "status-concluido";
                            if(strtolower($estado_texto) == 'concluído e entregue' || strtolower($estado_texto) == 'concluído e entregue') $classe_status = "status-concluido";
                    
                            ?>
                            <tr>
                                <td><?php echo $row['id_reparacao']; ?></td>
                                <!-- Mostra o nome do cliente se o Join funcionou, senão mostra o ID -->
                                <td><?php echo htmlspecialchars($row['nome_cliente'] ?? $row['']); ?></td>
                                <td><?php echo htmlspecialchars($row['equipamento']); ?></td>
                                <td>
    <?php 
        $descricao = htmlspecialchars($row['descricao_problema']);
        if (strlen($descricao) > 40) {
            // Corta o texto aos 40 caracteres e adiciona reticências
            echo substr($descricao, 0, 40) . '...';
        } else {
            echo $descricao;
        }
    ?>
</td>
                                <td>
                                    <span class="status-badge <?php echo $classe_status; ?>">
                                        <?php echo $estado_texto; ?>
                                    </span>
                                </td>
                                <td><?php echo $data; ?></td>
                                <td>
                                    <div class='actions-btns'>
                                        <a href='base_de_dados/ver_reparacao.php?id=<?php echo $row['id_reparacao']; ?>' class='btn-view' title="Ver">👁️</a>
                                        <a href='base_de_dados/editar_reparacao.php?id=<?php echo $row['id_reparacao']; ?>' class='btn-edit' title="Editar">✏️</a>
                                        <a href='base_de_dados/eliminar_reparacao.php?id=<?php echo $row['id_reparacao']; ?>' 
                                           class='btn-delete' title="Eliminar"
                                           onclick="return confirm('Tem a certeza que deseja eliminar esta reparação?')">🗑️</a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center;padding:30px;'>Nenhuma reparação registada.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>