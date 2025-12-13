<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/db.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $related_to_type = $_POST['related_to_type'];
    $related_to_id = $_POST['related_to_id'];
    $type = $_POST['type'];
    $notes = $_POST['notes'];
    $interaction_date = $_POST['interaction_date'];
    $user_id = $_SESSION['user_id'];
    $redirect_to = $_POST['redirect_to'] ?? 'index.php';

    $stmt = $pdo->prepare("INSERT INTO interactions (user_id, related_to_type, related_to_id, type, notes, interaction_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $related_to_type, $related_to_id, $type, $notes, $interaction_date]);

    header("Location: " . $redirect_to);
    exit;
}
header("Location: index.php");
