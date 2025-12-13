<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/csrf.php';
$title = "Edit Lead";
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: leads.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
$stmt->execute([$id]);
$lead = $stmt->fetch();

if (!$lead) {
    die("Lead not found.");
}

// Fetch users for assignment
$stmt = $pdo->query("SELECT id, name FROM users ORDER BY name ASC");
$users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = $_POST['name'];
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $source = $_POST['source'];
    $status = $_POST['status'];
    $score = $_POST['score'];
    $assigned_to = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;

    $stmt = $pdo->prepare("UPDATE leads SET name = ?, company_name = ?, email = ?, phone = ?, source = ?, status = ?, score = ?, assigned_to = ? WHERE id = ?");
    $stmt->execute([$name, $company_name, $email, $phone, $source, $status, $score, $assigned_to, $id]);

    header("Location: leads.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Edit Lead: <?php echo htmlspecialchars($lead['name']); ?></h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($lead['name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($lead['company_name']); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($lead['email']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($lead['phone']); ?>">
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Source</label>
                                <select name="source" class="form-select">
                                    <option value="Website" <?php if($lead['source'] == 'Website') echo 'selected'; ?>>Website</option>
                                    <option value="LinkedIn" <?php if($lead['source'] == 'LinkedIn') echo 'selected'; ?>>LinkedIn</option>
                                    <option value="Referral" <?php if($lead['source'] == 'Referral') echo 'selected'; ?>>Referral</option>
                                    <option value="Cold Call" <?php if($lead['source'] == 'Cold Call') echo 'selected'; ?>>Cold Call</option>
                                    <option value="Other" <?php if($lead['source'] == 'Other') echo 'selected'; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="New" <?php if($lead['status'] == 'New') echo 'selected'; ?>>New</option>
                                    <option value="Contacted" <?php if($lead['status'] == 'Contacted') echo 'selected'; ?>>Contacted</option>
                                    <option value="Qualified" <?php if($lead['status'] == 'Qualified') echo 'selected'; ?>>Qualified</option>
                                    <option value="Lost" <?php if($lead['status'] == 'Lost') echo 'selected'; ?>>Lost</option>
                                    <option value="Converted" <?php if($lead['status'] == 'Converted') echo 'selected'; ?>>Converted</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Score (0-100)</label>
                                <input type="number" name="score" class="form-control" value="<?php echo htmlspecialchars($lead['score']); ?>" min="0" max="100">
                            </div>
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Assigned To</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">-- Unassigned --</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php if($lead['assigned_to'] == $user['id']) echo 'selected'; ?>><?php echo htmlspecialchars($user['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Lead</button>
                        <a href="leads.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>

            <?php
            // Interactions Section
            $entity_type = 'lead';
            $entity_id = $id;
            include 'includes/interaction_history.php';
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
