<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/db.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $assigned_to = $_SESSION['user_id']; // Default to self

    $related_to_type = null;
    $related_to_id = null;

    if (!empty($_POST['related_company_id'])) {
        $related_to_type = 'company';
        $related_to_id = $_POST['related_company_id'];
    } elseif (!empty($_POST['related_lead_id'])) {
        $related_to_type = 'lead';
        $related_to_id = $_POST['related_lead_id'];
    }

    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, assigned_to, created_by, related_to_type, related_to_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $due_date, $assigned_to, $_SESSION['user_id'], $related_to_type, $related_to_id]);

    header("Location: tasks.php");
    exit;
}
header("Location: tasks.php");
