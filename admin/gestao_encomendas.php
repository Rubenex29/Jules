<?php
// admin/gestao_encomendas.php

session_start();
require_once '../includes/db.php';

// Proteger a página
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Lógica para ir buscar todas as encomendas
try {
    $sql = "
        SELECT 
            e.encomenda_id,
            c.nome AS cliente_nome,
            e.data_criacao,
            e.valor_total,
            est.nome_estado AS estado_atual
        FROM encomenda AS e
        JOIN cliente AS c ON e.cliente_id = c.cliente_id
        JOIN (
            SELECT he.encomenda_id, MAX(he.data_estado) AS max_data
            FROM historico_estado AS he
            GROUP BY he.encomenda_id
        ) AS ultimo_estado ON e.encomenda_id = ultimo_estado.encomenda_id
        JOIN historico_estado AS he_atual ON ultimo_estado.encomenda_id = he_atual.encomenda_id AND ultimo_estado.max_data = he_atual.data_estado
        JOIN estado_encomenda AS est ON he_atual.estado_id = est.estado_id
        ORDER BY e.data_criacao DESC";
    
    $stmt = $pdo->query($sql);
    $encomendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar as encomendas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Encomendas - CRM Admin</title>
    <link rel="stylesheet" href="../public/css/admin_panel.css">
    <link rel="stylesheet" href="../public/css/tabela.css">
</head>
<body>
    <div class="admin-panel-container">
        <aside class="sidebar">
            <h3>CRM Admin</h3>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="gestao_clientes.php">Gestão de Clientes</a>
                <a href="gestao_encomendas.php" class="active">Gestão de Encomendas</a>
                <a href="logout.php">Sair</a>
            </nav>
        </aside>
        <main class="content">
            <header>
                <h2>Gestão de Encomendas</h2>
            </header>
            
            <section class="tabela-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Valor Total</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($encomendas as $encomenda): ?>
                            <tr>
                                <td>#<?php echo $encomenda['encomenda_id']; ?></td>
                                <td><?php echo htmlspecialchars($encomenda['cliente_nome']); ?></td>
                                <td><?php echo date("d/m/Y", strtotime($encomenda['data_criacao'])); ?></td>
                                <td>€<?php echo number_format($encomenda['valor_total'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($encomenda['estado_atual']); ?></td>
                                <td>
                                    <a href="detalhe_encomenda.php?id=<?php echo $encomenda['encomenda_id']; ?>" class="btn-acao">Ver Detalhes</a>
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
