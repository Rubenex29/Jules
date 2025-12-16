<?php
// cliente/notificacoes.php

session_start();
require_once '../includes/db.php';

// Proteger a página
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

try {
    // Marcar todas as notificações "Pendentes" como "Vista" ao carregar a página
    $stmt_update = $pdo->prepare("UPDATE notificacao SET estado_envio = 'Vista' WHERE cliente_id = ? AND estado_envio = 'Pendente'");
    $stmt_update->execute([$cliente_id]);

    // Ir buscar todas as notificações do cliente, das mais recentes para as mais antigas
    $stmt_notificacoes = $pdo->prepare("SELECT * FROM notificacao WHERE cliente_id = ? ORDER BY data_envio DESC");
    $stmt_notificacoes->execute([$cliente_id]);
    $notificacoes = $stmt_notificacoes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar as notificações: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>As Minhas Notificações - CRM</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/painel.css">
    <link rel="stylesheet" href="../public/css/notificacoes.css">
</head>
<body>
    <div class="painel-container">
        <header class="painel-header">
            <h1>As Minhas Notificações</h1>
            <a href="painel.php" class="logout-btn">Voltar ao Painel</a>
        </header>

        <section class="notificacoes-lista">
            <?php if (count($notificacoes) > 0): ?>
                <ul>
                    <?php foreach ($notificacoes as $notificacao): ?>
                        <li class="notificacao-item estado-<?php echo strtolower(htmlspecialchars($notificacao['estado_envio'])); ?>">
                            <div class="notificacao-conteudo">
                                <p><?php echo htmlspecialchars($notificacao['conteudo']); ?></p>
                                <span class="notificacao-data"><?php echo date("d/m/Y H:i", strtotime($notificacao['data_envio'])); ?></span>
                            </div>
                            <div class="notificacao-estado">
                                <?php if ($notificacao['estado_envio'] !== 'Lida'): ?>
                                    <a href="marcar_lida.php?id=<?php echo $notificacao['notificacao_id']; ?>" class="btn-marcar-lida">Marcar como Lida</a>
                                <?php else: ?>
                                    <span>Lida</span>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Não tem nenhuma notificação.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
