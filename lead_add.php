<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/csrf.php';
$title = "Add Lead";
require_once 'includes/header.php';

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

    $stmt = $pdo->prepare("INSERT INTO leads (name, company_name, email, phone, source, status, score, assigned_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $company_name, $email, $phone, $source, $status, $score, $assigned_to]);

    header("Location: leads.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Add New Lead</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Source</label>
                                <select name="source" class="form-select">
                                    <option value="Website">Website</option>
                                    <option value="LinkedIn">LinkedIn</option>
                                    <option value="Referral">Referral</option>
                                    <option value="Cold Call">Cold Call</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="New">New</option>
                                    <option value="Contacted">Contacted</option>
                                    <option value="Qualified">Qualified</option>
                                    <option value="Lost">Lost</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Score (0-100)</label>
                                <input type="number" name="score" class="form-control" value="0" min="0" max="100">
                            </div>
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Assigned To</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">-- Unassigned --</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Lead</button>
                        <a href="leads.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
