<?php
// admin/detalhe_lead.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: gestao_leads.php");
    exit;
}

$lead_id = $_GET['id'];

// Registar Interação
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nova_interacao'])) {
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    $admin_id = $_SESSION['admin_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO interacao (lead_id, admin_id, tipo, descricao) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lead_id, $admin_id, $tipo, $descricao]);
        $msg = "<div class='alert alert-success'>Interação registada!</div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
    }
}

// Fetch Lead Data
$stmt = $pdo->prepare("SELECT * FROM lead WHERE lead_id = ?");
$stmt->execute([$lead_id]);
$lead = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lead) {
    die("Lead não encontrada.");
}

// Fetch Interações
$stmt_int = $pdo->prepare("
    SELECT i.*, a.nome as admin_nome
    FROM interacao i
    JOIN admin_user a ON i.admin_id = a.admin_id
    WHERE i.lead_id = ?
    ORDER BY i.data_interacao DESC
");
$stmt_int->execute([$lead_id]);
$interacoes = $stmt_int->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Detalhe Lead: <?php echo htmlspecialchars($lead['nome']); ?></h3>
        <a href="gestao_leads.php" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Informações</div>
                <div class="card-body">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($lead['email']); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($lead['telefone']); ?></p>
                    <p><strong>Empresa:</strong> <?php echo htmlspecialchars($lead['empresa']); ?></p>
                    <p><strong>Fonte:</strong> <?php echo htmlspecialchars($lead['fonte']); ?></p>
                    <p>
                        <strong>Status:</strong>
                        <span class="badge bg-info text-dark"><?php echo strtoupper($lead['status']); ?></span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Histórico de Interações</div>
                <div class="card-body">
                    <!-- Form nova interação -->
                    <form method="POST" class="mb-4 p-3 bg-light rounded border">
                        <h6>Registar Nova Interação</h6>
                        <input type="hidden" name="nova_interacao" value="1">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select name="tipo" class="form-select" required>
                                    <option value="chamada">Chamada</option>
                                    <option value="email">Email</option>
                                    <option value="reuniao">Reunião</option>
                                    <option value="nota">Nota Interna</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="text" name="descricao" class="form-control" placeholder="Resumo da interação..." required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Registar</button>
                            </div>
                        </div>
                    </form>

                    <!-- Lista -->
                    <div class="list-group">
                        <?php if (count($interacoes) == 0): ?>
                            <div class="list-group-item">Nenhuma interação registada.</div>
                        <?php endif; ?>

                        <?php foreach ($interacoes as $int): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-capitalize">
                                        <i class="bi <?php
                                            echo match($int['tipo']) {
                                                'chamada' => 'bi-telephone',
                                                'email' => 'bi-envelope',
                                                'reuniao' => 'bi-people',
                                                default => 'bi-sticky'
                                            };
                                        ?>"></i>
                                        <?php echo $int['tipo']; ?>
                                    </h6>
                                    <small><?php echo date('d/m/Y H:i', strtotime($int['data_interacao'])); ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($int['descricao']); ?></p>
                                <small class="text-muted">Por: <?php echo htmlspecialchars($int['admin_nome']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
