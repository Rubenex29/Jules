<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
            transition: all 0.3s;
        }
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 1.1rem;
            color: #d1d1d1;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: #f1f1f1;
            background-color: #495057;
        }
        .sidebar a.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .brand {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            color: #fff;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card-counter {
            box-shadow: 2px 2px 10px #DADADA;
            margin: 5px;
            padding: 20px 10px;
            background-color: #fff;
            height: 100px;
            border-radius: 5px;
            transition: .3s linear all;
        }
        .card-counter:hover {
            box-shadow: 4px 4px 20px #DADADA;
            transition: .3s linear all;
        }
        .card-counter.primary {
            background-color: #007bff;
            color: #FFF;
        }
        .card-counter.success {
            background-color: #198754;
            color: #FFF;
        }
        .card-counter.danger {
            background-color: #dc3545;
            color: #FFF;
        }
        .card-counter.info {
            background-color: #0dcaf0;
            color: #FFF;
        }
        .card-counter i {
            font-size: 5em;
            opacity: 0.2;
        }
        .card-counter .count-numbers {
            position: absolute;
            right: 35px;
            top: 20px;
            font-size: 32px;
            display: block;
        }
        .card-counter .count-name {
            position: absolute;
            right: 35px;
            top: 65px;
            font-style: italic;
            text-transform: capitalize;
            opacity: 0.5;
            display: block;
            font-size: 18px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .sidebar a {float: left;}
            .main-content {margin-left: 0;}
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="bi bi-building"></i> CRM Pro</div>
    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="gestao_clientes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'gestao_clientes.php' ? 'active' : ''; ?>"><i class="bi bi-people"></i> Clientes</a>
    <a href="gestao_leads.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'gestao_leads.php' ? 'active' : ''; ?>"><i class="bi bi-funnel"></i> Leads (Funnel)</a>
    <a href="gestao_encomendas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'gestao_encomendas.php' ? 'active' : ''; ?>"><i class="bi bi-box-seam"></i> Encomendas</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
</div>

<div class="main-content">
    <nav class="navbar navbar-light bg-light mb-4 rounded shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Área de Administração</span>
            <span class="navbar-text">
                <i class="bi bi-person-circle"></i> <?php echo isset($_SESSION['admin_nome']) ? htmlspecialchars($_SESSION['admin_nome']) : 'Utilizador'; ?>
            </span>
        </div>
    </nav>
