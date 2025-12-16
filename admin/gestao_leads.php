<?php
// admin/gestao_leads.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Processar formulário de nova lead
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'create') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $empresa = trim($_POST['empresa']);
    $fonte = trim($_POST['fonte']);
    $status = 'novo';

    if (!empty($nome)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO lead (nome, email, telefone, empresa, fonte, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $telefone, $empresa, $fonte, $status]);
            $msg = "<div class='alert alert-success'>Lead criada com sucesso!</div>";
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>Erro ao criar lead: " . $e->getMessage() . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'>O nome é obrigatório.</div>";
    }
}

// Processar atualização de estado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $lead_id = $_POST['lead_id'];
    $new_status = $_POST['status'];
    try {
        $stmt = $pdo->prepare("UPDATE lead SET status = ? WHERE lead_id = ?");
        $stmt->execute([$new_status, $lead_id]);
        $msg = "<div class='alert alert-success'>Estado atualizado!</div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
    }
}

// Listar Leads
$leads = $pdo->query("SELECT * FROM lead ORDER BY data_criacao DESC")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container-fluid">
    <h3>Gestão de Leads (Funnel)</h3>
    <?php echo $msg; ?>

    <div class="card mb-4">
        <div class="card-header">Nova Lead</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <input type="hidden" name="action" value="create">
                <div class="col-md-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Empresa</label>
                    <input type="text" name="empresa" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fonte</label>
                    <select name="fonte" class="form-select">
                        <option value="Site">Site</option>
                        <option value="LinkedIn">LinkedIn</option>
                        <option value="Referência">Referência</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Adicionar Lead</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Lista de Leads</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Empresa</th>
                            <th>Status</th>
                            <th>Fonte</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td>#<?php echo $lead['lead_id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($lead['nome']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($lead['email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($lead['empresa']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="lead_id" value="<?php echo $lead['lead_id']; ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                                        style="background-color: <?php
                                            echo match($lead['status']) {
                                                'novo' => '#e2e3e5',
                                                'contactado' => '#fff3cd',
                                                'qualificado' => '#cfe2ff',
                                                'ganho' => '#d1e7dd',
                                                'perdido' => '#f8d7da',
                                                default => '#fff'
                                            };
                                        ?>">
                                        <option value="novo" <?php echo $lead['status'] == 'novo' ? 'selected' : ''; ?>>Novo</option>
                                        <option value="contactado" <?php echo $lead['status'] == 'contactado' ? 'selected' : ''; ?>>Contactado</option>
                                        <option value="qualificado" <?php echo $lead['status'] == 'qualificado' ? 'selected' : ''; ?>>Qualificado</option>
                                        <option value="proposta" <?php echo $lead['status'] == 'proposta' ? 'selected' : ''; ?>>Proposta</option>
                                        <option value="negociacao" <?php echo $lead['status'] == 'negociacao' ? 'selected' : ''; ?>>Negociação</option>
                                        <option value="ganho" <?php echo $lead['status'] == 'ganho' ? 'selected' : ''; ?>>Ganho</option>
                                        <option value="perdido" <?php echo $lead['status'] == 'perdido' ? 'selected' : ''; ?>>Perdido</option>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo htmlspecialchars($lead['fonte']); ?></td>
                            <td>
                                <a href="detalhe_lead.php?id=<?php echo $lead['lead_id']; ?>" class="btn btn-sm btn-outline-info">Detalhes</a>
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
