<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/csrf.php';
$title = "Add Opportunity";
require_once 'includes/header.php';

// Fetch companies
$stmt = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC");
$companies = $stmt->fetchAll();

// Fetch stages
$stmt = $pdo->query("SELECT id, name FROM pipeline_stages ORDER BY display_order ASC");
$stages = $stmt->fetchAll();

// Fetch users
$stmt = $pdo->query("SELECT id, name FROM users ORDER BY name ASC");
$users = $stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title_input = $_POST['title'];
    $company_id = $_POST['company_id'];
    $value_estimated = $_POST['value_estimated'];
    $stage_id = $_POST['stage_id'];
    $close_date = $_POST['close_date'];
    $assigned_to = $_POST['assigned_to'];

    $stmt = $pdo->prepare("INSERT INTO opportunities (title, company_id, value_estimated, stage_id, close_date, assigned_to) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title_input, $company_id, $value_estimated, $stage_id, $close_date, $assigned_to]);

    header("Location: opportunities.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Add New Opportunity</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Deal Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Annual Service Contract">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">-- Select Company --</option>
                                    <?php foreach ($companies as $company): ?>
                                        <option value="<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimated Value ($)</label>
                                <input type="number" name="value_estimated" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stage</label>
                                <select name="stage_id" class="form-select" required>
                                    <?php foreach ($stages as $stage): ?>
                                        <option value="<?php echo $stage['id']; ?>"><?php echo htmlspecialchars($stage['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expected Close Date</label>
                                <input type="date" name="close_date" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                             <label class="form-label">Assigned To</label>
                             <select name="assigned_to" class="form-select">
                                <option value="">-- Select User --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Opportunity</button>
                        <a href="opportunities.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
