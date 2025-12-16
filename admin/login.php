<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRM Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            background: white;
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-header h3 {
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h3>CRM Admin</h3>
            <p class="text-muted">Faça login para continuar</p>
        </div>

        <?php
        session_start();
        if (isset($_SESSION['admin_error'])) {
            echo '<div class="alert alert-danger" role="alert">' . $_SESSION['admin_error'] . '</div>';
            unset($_SESSION['admin_error']);
        }
        ?>

        <form action="processa_login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="admin@crm.local">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
            </div>
        </form>
        <div class="mt-3 text-center">
            <a href="../index.php" class="text-decoration-none">Voltar ao Início</a>
        </div>
    </div>
</body>
</html>
