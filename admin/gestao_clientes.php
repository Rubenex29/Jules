<?php
// admin/gestao_clientes.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

try {
    // Note: Column name changed from 'data_registo' to 'data_criacao' based on new schema.sql
    // If working with old DB, this might break, but we are assuming schema.sql import.
    // Let's use * to be safe or check the schema.
    // In schema.sql I wrote `data_criacao`. In original file it was `data_registo`.
    // I will use `data_criacao` as per my new schema.
    $stmt = $pdo->query("SELECT * FROM cliente ORDER BY data_criacao DESC");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar os clientes: " . $e->getMessage());
}

include '../includes/header.php';
?>

<div class="container-fluid">
    <h3>Gestão de Clientes</h3>

    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <span>Lista de Clientes</span>
                <button class="btn btn-sm btn-primary" onclick="alert('Funcionalidade de adicionar cliente manual (TODO)')"><i class="bi bi-person-plus"></i> Novo Cliente</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Nome</th>
                            <th>Contacto</th>
                            <th>Empresa</th>
                            <th>Registado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?php echo $cliente['cliente_id']; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($cliente['nome']); ?></div>
                                </td>
                                <td>
                                    <div><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($cliente['email']); ?></div>
                                    <?php if(!empty($cliente['telefone'])): ?>
                                        <div class="small text-muted"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($cliente['telefone']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($cliente['empresa'] ?? '-'); ?></td>
                                <td><?php echo date("d/m/Y", strtotime($cliente['data_criacao'])); ?></td>
                                <td>
                                    <a href="detalhe_cliente.php?id=<?php echo $cliente['cliente_id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
