<?php
// cliente/editar_dados.php

session_start();
require_once '../includes/db.php';

// Proteger a página (temporariamente comentado para verificação do frontend)
/*
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit;
}
*/
$cliente_id = 1; // Usar um ID de cliente fixo para o teste

// Processar o formulário quando submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $morada = trim($_POST['morada']);

    // Validação
    if (empty($nome)) {
        $error_message = "O nome é obrigatório.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE cliente SET nome = ?, telefone = ?, morada = ? WHERE cliente_id = ?");
            $stmt->execute([$nome, $telefone, $morada, $cliente_id]);
            
            $_SESSION['success_message'] = "Dados atualizados com sucesso!";
            header("Location: painel.php");
            exit;
        } catch (PDOException $e) {
            $error_message = "Erro ao atualizar os dados. Tente novamente.";
        }
    }
}

// Ir buscar os dados atuais do cliente para exibir no formulário
try {
    $stmt = $pdo->prepare("SELECT nome, email, telefone, morada FROM cliente WHERE cliente_id = ?");
    $stmt->execute([$cliente_id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar os dados do cliente.");
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Dados - CRM</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Editar Dados Pessoais</h2>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="editar_dados.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" disabled>
                <small>O email não pode ser alterado.</small>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="morada">Morada</label>
                <textarea id="morada" name="morada" rows="3"><?php echo htmlspecialchars($cliente['morada'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Guardar Alterações</button>
            </div>
            <p><a href="painel.php">Voltar ao Painel</a></p>
        </form>
    </div>
</body>
</html>
