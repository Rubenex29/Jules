<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/csrf.php';
$title = "Editar Empresa";
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: companies.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$id]);
$company = $stmt->fetch();

if (!$company) {
    die("Company not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = $_POST['name'];
    $sector = $_POST['sector'];
    $size = $_POST['size'];
    $website = $_POST['website'];
    $address = $_POST['address'];

    $stmt = $pdo->prepare("UPDATE companies SET name = ?, sector = ?, size = ?, website = ?, address = ? WHERE id = ?");
    $stmt->execute([$name, $sector, $size, $website, $address, $id]);

    header("Location: companies.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Editar Empresa: <?php echo htmlspecialchars($company['name']); ?></h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Nome da Empresa</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($company['name']); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Setor</label>
                                <input type="text" name="sector" class="form-control" value="<?php echo htmlspecialchars($company['sector']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tamanho</label>
                                <select name="size" class="form-select">
                                    <option value="Small" <?php if($company['size'] == 'Small') echo 'selected'; ?>>Pequena</option>
                                    <option value="Medium" <?php if($company['size'] == 'Medium') echo 'selected'; ?>>Média</option>
                                    <option value="Large" <?php if($company['size'] == 'Large') echo 'selected'; ?>>Grande</option>
                                    <option value="Enterprise" <?php if($company['size'] == 'Enterprise') echo 'selected'; ?>>Enterprise</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Site</label>
                            <input type="url" name="website" class="form-control" value="<?php echo htmlspecialchars($company['website']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Endereço</label>
                            <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($company['address']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                        <a href="companies.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>

            <?php
            // Interactions Section
            $entity_type = 'company';
            $entity_id = $id;
            include 'includes/interaction_history.php';
            ?>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
