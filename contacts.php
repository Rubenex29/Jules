<?php
require_once 'includes/auth_check.php';
requireLogin();
$title = "Contacts";
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT contacts.*, companies.name as company_name FROM contacts LEFT JOIN companies ON contacts.company_id = companies.id ORDER BY contacts.name ASC");
$contacts = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Contacts</h2>
        <a href="contact_add.php" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add Contact</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Position</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($contact['name']); ?>
                                <?php if($contact['is_primary']): ?>
                                    <span class="badge bg-info text-dark">Primary</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($contact['company_name']); ?></td>
                            <td><a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></td>
                            <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                            <td><?php echo htmlspecialchars($contact['position']); ?></td>
                            <td>
                                <a href="contact_edit.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($contacts)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No contacts found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
