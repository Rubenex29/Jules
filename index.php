<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao CRM</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
        }
        .feature-card {
            transition: transform 0.3s;
            height: 100%;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 3rem;
            color: #0d6efd;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Gestão Inteligente de Negócios</h1>
            <p class="lead mb-4">Um CRM poderoso, dinâmico e fácil de usar para gerir os seus clientes, leads e vendas.</p>
        </div>
    </section>

    <!-- Cards de Acesso -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-5 mb-4">
                <div class="card feature-card p-4 text-center">
                    <div class="feature-icon"><i class="bi bi-shield-lock"></i></div>
                    <h3>Área de Administração</h3>
                    <p>Acesso restrito para gestores e equipa de vendas. Gerencie leads, clientes e relatórios.</p>
                    <a href="admin/login.php" class="btn btn-primary btn-lg mt-3">Login Admin</a>
                </div>
            </div>
            <div class="col-md-5 mb-4">
                <div class="card feature-card p-4 text-center">
                    <div class="feature-icon"><i class="bi bi-people"></i></div>
                    <h3>Área do Cliente</h3>
                    <p>Acesso para clientes acompanharem as suas encomendas e histórico.</p>
                    <a href="cliente/login.php" class="btn btn-outline-primary btn-lg mt-3">Login Cliente</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted border-top">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> CRM Pro. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>
