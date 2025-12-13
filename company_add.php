<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/csrf.php';
$title = "Nova Empresa";
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = $_POST['name'];
    $sector = $_POST['sector'];
    $size = $_POST['size'];
    $website = $_POST['website'];
    $address = $_POST['address'];

    $stmt = $pdo->prepare("INSERT INTO companies (name, sector, size, website, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $sector, $size, $website, $address]);

    header("Location: companies.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Nova Empresa</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Nome da Empresa</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Setor</label>
                                <input type="text" name="sector" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tamanho</label>
                                <select name="size" class="form-select">
                                    <option value="Small">Pequena</option>
                                    <option value="Medium">Média</option>
                                    <option value="Large">Grande</option>
                                    <option value="Enterprise">Enterprise</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Site (URL)</label>
                            <input type="url" name="website" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Endereço</label>
                            <textarea name="address" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="companies.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
