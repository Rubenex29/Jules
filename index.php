<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao CRM</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .container {
            text-align: center;
            background-color: #fff;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            font-size: 18px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-cliente {
            background-color: #007bff;
        }
        .btn-cliente:hover {
            background-color: #0056b3;
        }
        .btn-admin {
            background-color: #343a40;
        }
        .btn-admin:hover {
            background-color: #23272b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo ao Sistema de CRM</h1>
        <p>Selecione a área para a qual deseja entrar:</p>
        <div>
            <a href="cliente/login.php" class="btn btn-cliente">Área do Cliente</a>
            <a href="admin/login.php" class="btn btn-admin">Área de Administração</a>
        </div>
    </div>
</body>
</html>
