<?php
// cliente/marcar_lida.php

session_start();
require_once '../includes/db.php';

// Proteger a página
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];
$notificacao_id = $_GET['id'] ?? null;

if (!$notificacao_id) {
    // Se não for fornecido um ID, redireciona de volta
    header("Location: notificacoes.php");
    exit;
}

try {
    // Atualiza o estado da notificação para "Lida"
    // A condição `cliente_id = ?` é crucial para a segurança,
    // garantindo que um cliente não pode marcar a notificação de outro como lida.
    $stmt = $pdo->prepare("UPDATE notificacao SET estado_envio = 'Lida' WHERE notificacao_id = ? AND cliente_id = ?");
    $stmt->execute([$notificacao_id, $cliente_id]);

    // Redireciona de volta para a lista de notificações
    header("Location: notificacoes.php");
    exit;

} catch (PDOException $e) {
    die("Erro ao marcar a notificação como lida: " . $e->getMessage());
}
?>
