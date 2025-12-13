<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/csrf.php';
$title = "Add Contact";
require_once 'includes/header.php';

// Fetch companies for dropdown
$stmt = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC");
$companies = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = $_POST['name'];
    $company_id = !empty($_POST['company_id']) ? $_POST['company_id'] : null;
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $position = $_POST['position'];
    $is_primary = isset($_POST['is_primary']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO contacts (name, company_id, email, phone, position, is_primary) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $company_id, $email, $phone, $position, $is_primary]);

    header("Location: contacts.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Add New Contact</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <select name="company_id" class="form-select">
                                <option value="">-- No Company --</option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
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
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control">
                        </div>
                         <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary">
                            <label class="form-check-label" for="is_primary">Is Primary Contact?</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Contact</button>
                        <a href="contacts.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
