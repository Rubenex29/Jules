<?php
// cliente/processa_login.php

session_start();
require_once '../includes/db.php';

// Apenas processar se o método for POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validação básica
    if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Email ou password inválidos.";
        header("Location: login.php");
        exit;
    }

    try {
        // 1. Procurar o cliente pelo email
        $stmt = $pdo->prepare("SELECT cliente_id, nome, senha_hash FROM cliente WHERE email = ?");
        $stmt->execute([$email]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Verificar se o cliente existe e se a password está correta
        if ($cliente && password_verify($password, $cliente['senha_hash'])) {
            
            // 3. Autenticação bem-sucedida: regenerar ID da sessão por segurança
            session_regenerate_id(true);
            
            // 4. Guardar dados na sessão
            $_SESSION['cliente_id'] = $cliente['cliente_id'];
            $_SESSION['cliente_nome'] = $cliente['nome'];
            
            // 5. Redirecionar para o painel do cliente
            header("Location: painel.php");
            exit;

        } else {
            // Se as credenciais estiverem incorretas
            $_SESSION['error_message'] = "Email ou password incorretos.";
            header("Location: login.php");
            exit;
        }

    } catch (PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        $_SESSION['error_message'] = "Ocorreu um erro. Por favor, tente novamente mais tarde.";
        header("Location: login.php");
        exit;
    }

} else {
    // Redireciona se o acesso não for por POST
    header("Location: login.php");
    exit;
}
?>
