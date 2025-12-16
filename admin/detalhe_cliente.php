<?php
// admin/detalhe_cliente.php

session_start();
require_once '../includes/db.php';

// Proteger a página
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$cliente_id = $_GET['id'] ?? null;
if (!$cliente_id) {
    header("Location: gestao_clientes.php");
    exit;
}

// Lógica para ir buscar os detalhes do cliente e as suas encomendas
try {
    // Detalhes do cliente
    $stmt_cliente = $pdo->prepare("SELECT * FROM cliente WHERE cliente_id = ?");
    $stmt_cliente->execute([$cliente_id]);
    $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        header("Location: gestao_clientes.php");
        exit;
    }

    // Encomendas do cliente
    $stmt_encomendas = $pdo->prepare("SELECT encomenda_id, data_criacao, valor_total FROM encomenda WHERE cliente_id = ? ORDER BY data_criacao DESC");
    $stmt_encomendas->execute([$cliente_id]);
    $encomendas = $stmt_encomendas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar os detalhes do cliente: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhe do Cliente - CRM Admin</title>
    <link rel="stylesheet" href="../public/css/admin_panel.css">
    <link rel="stylesheet" href="../public/css/detalhe.css">
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
                <h2>Detalhe do Cliente #<?php echo htmlspecialchars($cliente['cliente_id']); ?></h2>
            </header>

            <section class="grid-container">
                <div class="card full-width">
                    <h4>Informações do Cliente</h4>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($cliente['nome']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($cliente['telefone'] ?? 'N/A'); ?></p>
                    <p><strong>Morada:</strong> <?php echo htmlspecialchars($cliente['morada'] ?? 'N/A'); ?></p>
                    <p><strong>Data de Registo:</strong> <?php echo date("d/m/Y", strtotime($cliente['data_registo'])); ?></p>
                </div>

                <div class="card full-width tabela-container">
                    <h4>Encomendas do Cliente</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Encomenda</th>
                                <th>Data</th>
                                <th>Valor Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($encomendas as $encomenda): ?>
                                <tr>
                                    <td>#<?php echo $encomenda['encomenda_id']; ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($encomenda['data_criacao'])); ?></td>
                                    <td>€<?php echo number_format($encomenda['valor_total'], 2, ',', '.'); ?></td>
                                    <td>
                                        <a href="detalhe_encomenda.php?id=<?php echo $encomenda['encomenda_id']; ?>" class="btn-acao">Ver Encomenda</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
