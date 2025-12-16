<?php
// admin/dashboard.php

session_start();
require_once '../includes/db.php';

// 1. Auth Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Fetch Stats
try {
    // Total Clients
    $total_clientes = $pdo->query("SELECT COUNT(*) FROM cliente")->fetchColumn();
    // Total Orders
    $total_encomendas = $pdo->query("SELECT COUNT(*) FROM encomenda")->fetchColumn();
    // Total Leads
    $total_leads = $pdo->query("SELECT COUNT(*) FROM lead")->fetchColumn();
    // Total Revenue
    $total_faturado = $pdo->query("SELECT SUM(valor_total) FROM encomenda")->fetchColumn();
    $total_faturado = $total_faturado ?? 0;

    // Chart Data: Orders by Status
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
    $stats_estados = $stmt_estados->fetchAll(PDO::FETCH_ASSOC);

    // Chart Data Preparation
    $labels_chart = [];
    $data_chart = [];
    foreach($stats_estados as $st) {
        $labels_chart[] = $st['nome_estado'];
        $data_chart[] = $st['total'];
    }

} catch (PDOException $e) {
    die("Erro DB: " . $e->getMessage());
}

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card-counter primary position-relative">
                <i class="bi bi-people-fill"></i>
                <span class="count-numbers"><?php echo $total_clientes; ?></span>
                <span class="count-name">Clientes</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-counter danger position-relative">
                <i class="bi bi-cart-fill"></i>
                <span class="count-numbers"><?php echo $total_encomendas; ?></span>
                <span class="count-name">Encomendas</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-counter success position-relative">
                <i class="bi bi-currency-euro"></i>
                <span class="count-numbers"><?php echo number_format($total_faturado, 0, ',', '.'); ?></span>
                <span class="count-name">Faturado</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-counter info position-relative">
                <i class="bi bi-funnel-fill"></i>
                <span class="count-numbers"><?php echo $total_leads; ?></span>
                <span class="count-name">Leads Ativas</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Estado das Encomendas</h5>
                </div>
                <div class="card-body">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Ações Rápidas</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="gestao_clientes.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-person-plus"></i> Novo Cliente
                    </a>
                    <a href="gestao_leads.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-funnel"></i> Gerir Leads
                    </a>
                    <a href="gestao_encomendas.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-box-seam"></i> Ver Encomendas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels_chart); ?>,
            datasets: [{
                label: 'Número de Encomendas',
                data: <?php echo json_encode($data_chart); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
