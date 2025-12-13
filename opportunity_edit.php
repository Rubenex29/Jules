<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/csrf.php';
$title = "Edit Opportunity";
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: opportunities.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM opportunities WHERE id = ?");
$stmt->execute([$id]);
$opportunity = $stmt->fetch();

if (!$opportunity) {
    die("Opportunity not found.");
}

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
    $value_real = $_POST['value_real'];
    $stage_id = $_POST['stage_id'];
    $close_date = $_POST['close_date'];
    $assigned_to = $_POST['assigned_to'];

    $stmt = $pdo->prepare("UPDATE opportunities SET title = ?, company_id = ?, value_estimated = ?, value_real = ?, stage_id = ?, close_date = ?, assigned_to = ? WHERE id = ?");
    $stmt->execute([$title_input, $company_id, $value_estimated, $value_real, $stage_id, $close_date, $assigned_to, $id]);

    header("Location: opportunities.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Edit Opportunity: <?php echo htmlspecialchars($opportunity['title']); ?></h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Deal Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($opportunity['title']); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">-- Select Company --</option>
                                    <?php foreach ($companies as $company): ?>
                                        <option value="<?php echo $company['id']; ?>" <?php if($opportunity['company_id'] == $company['id']) echo 'selected'; ?>><?php echo htmlspecialchars($company['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimated Value ($)</label>
                                <input type="number" name="value_estimated" class="form-control" step="0.01" value="<?php echo htmlspecialchars($opportunity['value_estimated']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stage</label>
                                <select name="stage_id" class="form-select" required>
                                    <?php foreach ($stages as $stage): ?>
                                        <option value="<?php echo $stage['id']; ?>" <?php if($opportunity['stage_id'] == $stage['id']) echo 'selected'; ?>><?php echo htmlspecialchars($stage['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expected Close Date</label>
                                <input type="date" name="close_date" class="form-control" value="<?php echo htmlspecialchars($opportunity['close_date']); ?>">
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Real Value ($) (if closed)</label>
                                <input type="number" name="value_real" class="form-control" step="0.01" value="<?php echo htmlspecialchars($opportunity['value_real']); ?>">
                            </div>
                             <div class="col-md-6 mb-3">
                                 <label class="form-label">Assigned To</label>
                                 <select name="assigned_to" class="form-select">
                                    <option value="">-- Select User --</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php if($opportunity['assigned_to'] == $user['id']) echo 'selected'; ?>><?php echo htmlspecialchars($user['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Opportunity</button>
                        <a href="opportunities.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>

             <?php
            // Interactions Section
            $entity_type = 'opportunity';
            $entity_id = $id;
            include 'includes/interaction_history.php';
            ?>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
