<?php
require_once 'includes/auth_check.php';
requireLogin();
$title = "Dashboard";
require_once 'includes/header.php';

// Fetch metrics
try {
    // Total Leads
    $stmt = $pdo->query("SELECT COUNT(*) FROM leads");
    $total_leads = $stmt->fetchColumn();

    // Total Opportunities Value
    $stmt = $pdo->query("SELECT SUM(value_estimated) FROM opportunities");
    $pipeline_value = $stmt->fetchColumn() ?: 0;

    // Opportunities Won
    $stmt = $pdo->query("SELECT COUNT(*) FROM opportunities JOIN pipeline_stages ON opportunities.stage_id = pipeline_stages.id WHERE pipeline_stages.name = 'Fechado'");
    $won_opps = $stmt->fetchColumn();

    // Chart Data
    $stmt = $pdo->query("SELECT ps.name, COUNT(o.id) as count FROM pipeline_stages ps LEFT JOIN opportunities o ON ps.id = o.stage_id GROUP BY ps.id, ps.name, ps.display_order ORDER BY ps.display_order");
    $stage_data = $stmt->fetchAll();
    $chart_labels = [];
    $chart_data = [];
    foreach($stage_data as $row) {
        $chart_labels[] = $row['name'];
        $chart_data[] = $row['count'];
    }

    // Pending Tasks
    $stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status != 'Completed'");
    $pending_tasks = $stmt->fetchColumn();

} catch (PDOException $e) {
    $total_leads = $pipeline_value = $won_opps = $pending_tasks = 0;
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-dashboard shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Leads</h5>
                    <h2 class="card-text"><?php echo $total_leads; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dashboard shadow-sm" style="border-left-color: #198754;">
                <div class="card-body">
                    <h5 class="card-title text-muted">Pipeline Value</h5>
                    <h2 class="card-text">$<?php echo number_format($pipeline_value, 2); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dashboard shadow-sm" style="border-left-color: #ffc107;">
                <div class="card-body">
                    <h5 class="card-title text-muted">Won Deals</h5>
                    <h2 class="card-text"><?php echo $won_opps; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dashboard shadow-sm" style="border-left-color: #dc3545;">
                <div class="card-body">
                    <h5 class="card-title text-muted">Pending Tasks</h5>
                    <h2 class="card-text"><?php echo $pending_tasks; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    Pipeline Overview
                </div>
                <div class="card-body">
                    <canvas id="pipelineChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    Recent Activities
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <!-- Placeholder for activities -->
                        <li class="list-group-item">User added a new lead <small class="text-muted float-end">2m ago</small></li>
                        <li class="list-group-item">Meeting scheduled with ACME Corp <small class="text-muted float-end">1h ago</small></li>
                        <li class="list-group-item">Proposal sent to TechStart <small class="text-muted float-end">3h ago</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('pipelineChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: '# de Oportunidades',
                data: <?php echo json_encode($chart_data); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
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
});
</script>

<?php include 'includes/footer.php'; ?>
