<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/csrf.php';
$title = "Tasks";
require_once 'includes/header.php';

$stmt = $pdo->prepare("
    SELECT tasks.*, users.name as assigned_user,
           companies.name as company_name,
           leads.name as lead_name
    FROM tasks
    LEFT JOIN users ON tasks.assigned_to = users.id
    LEFT JOIN companies ON tasks.related_to_type = 'company' AND tasks.related_to_id = companies.id
    LEFT JOIN leads ON tasks.related_to_type = 'lead' AND tasks.related_to_id = leads.id
    WHERE tasks.assigned_to = ? OR tasks.assigned_to IS NULL
    ORDER BY due_date ASC
");
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll();

// Fetch companies and leads for dropdowns
$companies = $pdo->query("SELECT id, name FROM companies ORDER BY name")->fetchAll();
$leads = $pdo->query("SELECT id, name FROM leads ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    verify_csrf();
    $taskId = $_POST['task_id'];
    $stmt = $pdo->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ?");
    $stmt->execute([$taskId]);
    header("Location: tasks.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Tasks</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="bi bi-check2-plus"></i> Add Task
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
             <ul class="list-group list-group-flush">
                <?php foreach ($tasks as $task): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center <?php echo ($task['status'] == 'Completed') ? 'bg-light text-muted' : ''; ?>">
                    <div>
                        <h6 class="mb-1 <?php echo ($task['status'] == 'Completed') ? 'text-decoration-line-through' : ''; ?>">
                            <?php echo htmlspecialchars($task['title']); ?>
                        </h6>
                        <small class="text-muted">Due: <?php echo $task['due_date']; ?></small>
                        <?php if($task['description']): ?>
                        <p class="mb-0 small"><?php echo htmlspecialchars($task['description']); ?></p>
                        <?php endif; ?>

                        <?php if($task['company_name']): ?>
                            <span class="badge bg-light text-dark border"><i class="bi bi-building"></i> <?php echo htmlspecialchars($task['company_name']); ?></span>
                        <?php endif; ?>
                        <?php if($task['lead_name']): ?>
                            <span class="badge bg-light text-dark border"><i class="bi bi-funnel"></i> <?php echo htmlspecialchars($task['lead_name']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if($task['status'] != 'Completed'): ?>
                        <form method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Complete</button>
                        </form>
                        <?php else: ?>
                            <span class="badge bg-success">Done</span>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
                 <?php if (empty($tasks)): ?>
                    <li class="list-group-item text-center">No tasks found.</li>
                <?php endif; ?>
             </ul>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
             <form action="task_add_handler.php" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>

                    <hr>
                    <h6 class="mb-3">Related To (Optional)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company</label>
                            <select name="related_company_id" class="form-select">
                                <option value="">-- None --</option>
                                <?php foreach($companies as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label class="form-label">Lead</label>
                            <select name="related_lead_id" class="form-select">
                                <option value="">-- None --</option>
                                <?php foreach($leads as $l): ?>
                                    <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
