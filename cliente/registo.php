<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo de Cliente - CRM</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>

    <div class="form-container">
        <h2>Criar Conta de Cliente</h2>
        <form action="processa_registo.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone">
            </div>
            <div class="form-group">
                <label for="morada">Morada</label>
                <textarea id="morada" name="morada" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Registar</button>
            </div>
            <p>Já tem conta? <a href="login.php">Faça login aqui</a>.</p>
        </form>
    </div>

</body>
</html>
