<?php
require_once 'includes/auth_check.php';
requireLogin();
$title = "Opportunities";
require_once 'includes/header.php';

// Fetch stages
$stmt = $pdo->query("SELECT * FROM pipeline_stages ORDER BY display_order ASC");
$stages = $stmt->fetchAll();

// Fetch opportunities
$stmt = $pdo->query("SELECT opportunities.*, companies.name as company_name FROM opportunities LEFT JOIN companies ON opportunities.company_id = companies.id");
$opportunities = $stmt->fetchAll();

// Group opportunities by stage
$grouped_opportunities = [];
foreach ($stages as $stage) {
    $grouped_opportunities[$stage['id']] = [];
}
foreach ($opportunities as $opp) {
    $grouped_opportunities[$opp['stage_id']][] = $opp;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Opportunities (Kanban)</h2>
        <a href="opportunity_add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Deal</a>
    </div>

    <div class="row flex-nowrap overflow-auto pb-4">
        <?php foreach ($stages as $stage): ?>
        <div class="col-md-3" style="min-width: 300px;">
            <div class="card bg-light h-100 shadow-sm">
                <div class="card-header fw-bold text-center">
                    <?php echo htmlspecialchars($stage['name']); ?>
                    <span class="badge bg-secondary rounded-pill float-end"><?php echo count($grouped_opportunities[$stage['id']]); ?></span>
                </div>
                <div class="card-body" style="min-height: 500px;">
                    <?php foreach ($grouped_opportunities[$stage['id']] as $opp): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><a href="opportunity_edit.php?id=<?php echo $opp['id']; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($opp['title']); ?></a></h6>
                            <p class="card-text text-muted small mb-2"><i class="bi bi-building"></i> <?php echo htmlspecialchars($opp['company_name']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-success">$<?php echo number_format($opp['value_estimated']); ?></span>
                                <small class="text-muted"><?php echo $opp['close_date']; ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
