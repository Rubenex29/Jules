<?php
require_once 'includes/auth_check.php';
requireLogin();
require_once 'includes/db.php';

$type = $_GET['type'] ?? 'contacts';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_export_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

if ($type == 'contacts') {
    fputcsv($output, ['ID', 'Name', 'Company', 'Email', 'Phone', 'Position']);
    $stmt = $pdo->query("SELECT c.id, c.name, co.name as company_name, c.email, c.phone, c.position FROM contacts c LEFT JOIN companies co ON c.company_id = co.id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
} elseif ($type == 'leads') {
    fputcsv($output, ['ID', 'Name', 'Company', 'Email', 'Status', 'Score', 'Source']);
    $stmt = $pdo->query("SELECT id, name, company_name, email, status, score, source FROM leads");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
} elseif ($type == 'companies') {
     fputcsv($output, ['ID', 'Name', 'Sector', 'Size', 'Website']);
    $stmt = $pdo->query("SELECT id, name, sector, size, website FROM companies");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit;
