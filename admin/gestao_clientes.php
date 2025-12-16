<?php
// admin/gestao_clientes.php

session_start();
require_once '../includes/db.php';

// Proteger a página (temporariamente comentado para verificação do frontend)
/*
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
*/

// Lógica para ir buscar todos os clientes
try {
    $stmt = $pdo->query("SELECT cliente_id, nome, email, data_registo FROM cliente ORDER BY data_registo DESC");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar os clientes: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Clientes - CRM Admin</title>
    <link rel="stylesheet" href="../public/css/admin_panel.css">
    <link rel="stylesheet" href="../public/css/tabela.css">
</head>
<body>
    <div class="admin-panel-container">
        <aside class="sidebar">
            <h3>CRM Admin</h3>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="gestao_clientes.php" class="active">Gestão de Clientes</a>
                <a href="gestao_encomendas.php">Gestão de Encomendas</a>
                <a href="logout.php">Sair</a>
            </nav>
        </aside>
        <main class="content">
            <header>
                <h2>Gestão de Clientes</h2>
            </header>
            
            <section class="tabela-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Cliente</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Data de Registo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?php echo $cliente['cliente_id']; ?></td>
                                <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                <td><?php echo date("d/m/Y", strtotime($cliente['data_registo'])); ?></td>
                                <td>
                                    <a href="detalhe_cliente.php?id=<?php echo $cliente['cliente_id']; ?>" class="btn-acao">Ver Detalhes</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
