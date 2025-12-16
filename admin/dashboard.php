<?php
// admin/dashboard.php

session_start();
require_once '../includes/db.php';

// 1. Proteger a página: verificar se o admin está autenticado
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Consultas para obter as estatísticas
try {
    // Total de Clientes
    $total_clientes = $pdo->query("SELECT COUNT(*) FROM cliente")->fetchColumn();

    // Total de Encomendas
    $total_encomendas = $pdo->query("SELECT COUNT(*) FROM encomenda")->fetchColumn();

    // Valor Total Faturado
    $total_faturado = $pdo->query("SELECT SUM(valor_total) FROM encomenda")->fetchColumn();
    $total_faturado = $total_faturado ?? 0; // Se for null, define como 0

    // Encomendas por Estado (Query corrigida)
    $sql_estados = "
        SELECT 
            est.nome_estado, 
            COUNT(ultimo_estado.encomenda_id) AS total
        FROM estado_encomenda AS est
        LEFT JOIN (
            SELECT 
                he.encomenda_id,
                he.estado_id,
                ROW_NUMBER() OVER(PARTITION BY he.encomenda_id ORDER BY he.data_estado DESC) as rn
            FROM historico_estado he
        ) AS ultimo_estado ON est.estado_id = ultimo_estado.estado_id AND ultimo_estado.rn = 1
        GROUP BY est.nome_estado
        ORDER BY est.ordem;
    ";
    $stmt_estados = $pdo->query($sql_estados);
    $encomendas_por_estado = $stmt_estados->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar as estatísticas do dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CRM Admin</title>
    <link rel="stylesheet" href="../public/css/admin_panel.css">
</head>
<body>
    <div class="admin-panel-container">
        <aside class="sidebar">
            <h3>CRM Admin</h3>
            <nav>
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="gestao_clientes.php">Gestão de Clientes</a>
                <a href="gestao_encomendas.php">Gestão de Encomendas</a>
                <a href="logout.php">Sair</a>
            </nav>
        </aside>
        <main class="content">
            <header>
                <h2>Dashboard</h2>
                <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['admin_nome']); ?>!</p>
            </header>
            
            <section class="stats-cards">
                <div class="card">
                    <h4>Total de Clientes</h4>
                    <p><?php echo $total_clientes; ?></p>
                </div>
                <div class="card">
                    <h4>Total de Encomendas</h4>
                    <p><?php echo $total_encomendas; ?></p>
                </div>
                <div class="card">
                    <h4>Valor Total Faturado</h4>
                    <p>€<?php echo number_format($total_faturado, 2, ',', '.'); ?></p>
                </div>
            </section>

            <section class="encomendas-por-estado">
                <h3>Encomendas por Estado</h3>
                <ul>
                    <?php foreach ($encomendas_por_estado as $estado): ?>
                        <li>
                            <span class="estado-nome"><?php echo htmlspecialchars($estado['nome_estado']); ?></span>
                            <span class="estado-total"><?php echo $estado['total']; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        </main>
    </div>
</body>
</html>
