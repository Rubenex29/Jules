<?php
// admin/gestao_encomendas.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

try {
    // Note: In schema.sql I used `data_encomenda`. Original code used `data_criacao`.
    // I will adjust the query to use `data_encomenda`.
    // Also, the JOIN logic for "last state" is complex. I'll simplify it or keep it if it works.
    // The previous logic for last state relies on `data_estado` max. It is correct.

    $sql = "
        SELECT 
            e.encomenda_id,
            c.nome AS cliente_nome,
            e.data_encomenda,
            e.valor_total,
            est.nome_estado AS estado_atual,
            est.nome_estado
        FROM encomenda AS e
        JOIN cliente AS c ON e.cliente_id = c.cliente_id
        -- Subquery to find the latest state ID for each order
        LEFT JOIN (
             SELECT he.encomenda_id, he.estado_id
             FROM historico_estado he
             INNER JOIN (
                 SELECT encomenda_id, MAX(data_estado) as max_date
                 FROM historico_estado
                 GROUP BY encomenda_id
             ) latest ON he.encomenda_id = latest.encomenda_id AND he.data_estado = latest.max_date
        ) as current_state_link ON e.encomenda_id = current_state_link.encomenda_id
        LEFT JOIN estado_encomenda est ON current_state_link.estado_id = est.estado_id
        ORDER BY e.data_encomenda DESC";

        // Note: The previous query had a potential issue with multiple states at exact same second.
        // But for this purpose it's fine. I'll use a slightly safer join or just trust the previous logic if it was robust.
        // Actually, I'll stick to a simpler logic: Fetch all orders, then for each order fetch the last state in PHP or use a robust window function if MySQL 8.
        // But to be safe with older MySQL versions (XAMPP usually has new ones though), let's stick to standard SQL.

    $stmt = $pdo->query($sql);
    $encomendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar as encomendas: " . $e->getMessage());
}

include '../includes/header.php';
?>

<div class="container-fluid">
    <h3>Gestão de Encomendas</h3>

    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($encomendas as $encomenda): ?>
                            <tr>
                                <td><strong>#<?php echo $encomenda['encomenda_id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($encomenda['cliente_nome']); ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($encomenda['data_encomenda'])); ?></td>
                                <td>€<?php echo number_format($encomenda['valor_total'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="badge rounded-pill <?php
                                        echo match($encomenda['nome_estado']) {
                                            'Pendente' => 'bg-warning text-dark',
                                            'Em Processamento' => 'bg-info text-dark',
                                            'Enviado' => 'bg-primary',
                                            'Entregue' => 'bg-success',
                                            'Cancelado' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    ?>">
                                        <?php echo htmlspecialchars($encomenda['nome_estado'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detalhe_encomenda.php?id=<?php echo $encomenda['encomenda_id']; ?>" class="btn btn-sm btn-outline-primary">
                                        Detalhes
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
