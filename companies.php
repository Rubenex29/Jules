<?php
require_once 'includes/auth_check.php';
requireLogin();
$title = "Empresas";
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM companies ORDER BY created_at DESC");
$companies = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Empresas</h2>
        <a href="company_add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nova Empresa</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Setor</th>
                            <th>Tamanho</th>
                            <th>Site</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $company): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($company['name']); ?></td>
                            <td><?php echo htmlspecialchars($company['sector']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($company['size']); ?></span></td>
                            <td>
                                <?php if($company['website']): ?>
                                    <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank"><i class="bi bi-link-45deg"></i></a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="company_edit.php?id=<?php echo $company['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($companies)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhuma empresa encontrada.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
