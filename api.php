<?php
// Simple REST API Endpoint
// Usage: GET /api.php/leads or GET /api.php/companies
// Authentication: Bearer Token (Simulated with simple check or Session)
// For this MVP, we will use the existing Session auth (Cookie-based API) or a simple API Key if we implemented it.
// Let's use Session for simplicity in this browser-based context, but structure it as JSON.

require_once 'includes/config.php';
require_once 'includes/db.php';

header("Content-Type: application/json");

// Basic Auth Check (Session or Token)
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path_info = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];
$resource = $path_info[0] ?? null;
$id = $path_info[1] ?? null;

if ($method === 'GET') {
    if ($resource === 'leads') {
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
        } else {
            $stmt = $pdo->query("SELECT * FROM leads");
            $data = $stmt->fetchAll();
        }
        echo json_encode($data);
    } elseif ($resource === 'companies') {
         if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
        } else {
            $stmt = $pdo->query("SELECT * FROM companies");
            $data = $stmt->fetchAll();
        }
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Resource not found"]);
    }
} elseif ($method === 'POST') {
    // Example: Create Lead
    if ($resource === 'leads') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid JSON"]);
            exit;
        }
        // Basic validation and insertion logic would go here
        // ...
        echo json_encode(["status" => "success", "message" => "Lead created (simulation)"]);
    } else {
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
