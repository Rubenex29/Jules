<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/csrf.php';
$title = "Edit Contact";
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: contacts.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$id]);
$contact = $stmt->fetch();

if (!$contact) {
    die("Contact not found.");
}

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

    $stmt = $pdo->prepare("UPDATE contacts SET name = ?, company_id = ?, email = ?, phone = ?, position = ?, is_primary = ? WHERE id = ?");
    $stmt->execute([$name, $company_id, $email, $phone, $position, $is_primary, $id]);

    header("Location: contacts.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Edit Contact: <?php echo htmlspecialchars($contact['name']); ?></h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                         <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($contact['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <select name="company_id" class="form-select">
                                <option value="">-- No Company --</option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?php echo $company['id']; ?>" <?php if($company['id'] == $contact['company_id']) echo 'selected'; ?>><?php echo htmlspecialchars($company['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($contact['email']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($contact['phone']); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($contact['position']); ?>">
                        </div>
                         <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" <?php if($contact['is_primary']) echo 'checked'; ?>>
                            <label class="form-check-label" for="is_primary">Is Primary Contact?</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Contact</button>
                        <a href="contacts.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
