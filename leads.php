<?php
require_once 'includes/auth_check.php';
requireLogin();
$title = "Leads";
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT leads.*, users.name as assigned_user FROM leads LEFT JOIN users ON leads.assigned_to = users.id ORDER BY leads.created_at DESC");
$leads = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Leads</h2>
        <a href="lead_add.php" class="btn btn-primary"><i class="bi bi-funnel"></i> Add Lead</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Assigned To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lead['name']); ?></td>
                            <td><?php echo htmlspecialchars($lead['company_name']); ?></td>
                            <td>
                                <span class="badge bg-<?php
                                    echo match($lead['status']) {
                                        'New' => 'primary',
                                        'Contacted' => 'info',
                                        'Qualified' => 'success',
                                        'Lost' => 'danger',
                                        'Converted' => 'secondary',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($lead['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($lead['score']); ?></td>
                            <td><?php echo htmlspecialchars($lead['assigned_user'] ?? 'Unassigned'); ?></td>
                            <td>
                                <a href="lead_edit.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($leads)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No leads found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
