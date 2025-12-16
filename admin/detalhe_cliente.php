<?php
// admin/detalhe_cliente.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$cliente_id = $_GET['id'] ?? null;
if (!$cliente_id) {
    header("Location: gestao_clientes.php");
    exit;
}

// Fetch Client Info
$stmt = $pdo->prepare("SELECT * FROM cliente WHERE cliente_id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die("Cliente não encontrado.");
}

// Fetch Client Orders
$stmt_enc = $pdo->prepare("SELECT * FROM encomenda WHERE cliente_id = ? ORDER BY data_encomenda DESC"); // using data_encomenda as per my schema
// If schema uses data_criacao for orders, adjust here.
// My schema.sql used data_encomenda.
$stmt_enc->execute([$cliente_id]);
$encomendas = $stmt_enc->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Cliente: <?php echo htmlspecialchars($cliente['nome']); ?></h3>
        <a href="gestao_clientes.php" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">Dados do Cliente</div>
                <div class="card-body">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($cliente['telefone']); ?></p>
                    <p><strong>Empresa:</strong> <?php echo htmlspecialchars($cliente['empresa']); ?></p>
                    <p><strong>NIF:</strong> <?php echo htmlspecialchars($cliente['nif']); ?></p>
                    <p><strong>Morada:</strong><br><?php echo nl2br(htmlspecialchars($cliente['morada'])); ?></p>
                    <p class="text-muted small">Registado em: <?php echo date("d/m/Y", strtotime($cliente['data_criacao'])); ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">Histórico de Encomendas</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($encomendas) > 0): ?>
                                    <?php foreach ($encomendas as $encomenda): ?>
                                        <tr>
                                            <td>#<?php echo $encomenda['encomenda_id']; ?></td>
                                            <td><?php echo date("d/m/Y", strtotime($encomenda['data_encomenda'])); ?></td>
                                            <td>€<?php echo number_format($encomenda['valor_total'], 2, ',', '.'); ?></td>
                                            <td>
                                                <a href="detalhe_encomenda.php?id=<?php echo $encomenda['encomenda_id']; ?>" class="btn btn-sm btn-outline-info">
                                                    Ver Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Sem encomendas registadas.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
