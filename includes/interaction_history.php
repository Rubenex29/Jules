<?php
// Function to get interactions for an entity
function getInteractions($pdo, $type, $id) {
    $stmt = $pdo->prepare("
        SELECT interactions.*, users.name as user_name
        FROM interactions
        LEFT JOIN users ON interactions.user_id = users.id
        WHERE related_to_type = ? AND related_to_id = ?
        ORDER BY interaction_date DESC
    ");
    $stmt->execute([$type, $id]);
    return $stmt->fetchAll();
}
?>

<div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Histórico de Interações</h5>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addInteractionModal">
            <i class="bi bi-plus-lg"></i> Nova Interação
        </button>
    </div>
    <div class="card-body">
        <div class="timeline">
            <?php
            $interactions = getInteractions($pdo, $entity_type, $entity_id);
            if (count($interactions) > 0):
                foreach ($interactions as $interaction):
            ?>
                <div class="card mb-3 border-start border-4 border-<?php
                    echo match($interaction['type']) {
                        'Call' => 'info',
                        'Meeting' => 'warning',
                        'Email' => 'primary',
                        default => 'secondary'
                    };
                ?>">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title mb-1">
                                <?php
                                $icon = match($interaction['type']) {
                                    'Call' => 'telephone',
                                    'Meeting' => 'people',
                                    'Email' => 'envelope',
                                    default => 'sticky'
                                };
                                ?>
                                <i class="bi bi-<?php echo $icon; ?>"></i> <?php echo htmlspecialchars($interaction['type']); ?>
                            </h6>
                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($interaction['interaction_date'])); ?></small>
                        </div>
                        <p class="card-text mb-1"><?php echo nl2br(htmlspecialchars($interaction['notes'])); ?></p>
                        <small class="text-muted">Registado por: <?php echo htmlspecialchars($interaction['user_name'] ?? 'Sistema'); ?></small>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <p class="text-muted text-center">Nenhuma interação registada.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addInteractionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registar Nova Interação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="interaction_add.php" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="related_to_type" value="<?php echo $entity_type; ?>">
                <input type="hidden" name="related_to_id" value="<?php echo $entity_id; ?>">
                <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select" required>
                            <option value="Call">Chamada</option>
                            <option value="Email">Email</option>
                            <option value="Meeting">Reunião</option>
                            <option value="Note">Nota</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data</label>
                        <input type="datetime-local" name="interaction_date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
