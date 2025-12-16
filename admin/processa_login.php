<?php
// admin/processa_login.php

session_start();
require_once '../includes/db.php';

// Apenas processar se o método for POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validação
    if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['admin_error'] = "Email ou password inválidos.";
        header("Location: login.php");
        exit;
    }

    try {
        // 1. Procurar o administrador pelo email
        $stmt = $pdo->prepare("SELECT admin_id, nome, permissao, senha_hash FROM admin_user WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Verificar se existe e se a password está correta
        if ($admin && password_verify($password, $admin['senha_hash'])) {
            
            // 3. Autenticação bem-sucedida: regenerar ID da sessão
            session_regenerate_id(true);
            
            // 4. Guardar dados na sessão de administrador (com um prefixo para evitar colisões)
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_nome'] = $admin['nome'];
            $_SESSION['admin_permissao'] = $admin['permissao'];
            
            // 5. Redirecionar para o dashboard
            header("Location: dashboard.php");
            exit;

        } else {
            $_SESSION['admin_error'] = "Credenciais incorretas.";
            header("Location: login.php");
            exit;
        }

    } catch (PDOException $e) {
        error_log("Erro no login de admin: " . $e->getMessage());
        $_SESSION['admin_error'] = "Ocorreu um erro no servidor.";
        header("Location: login.php");
        exit;
    }

} else {
    // Redireciona se o acesso não for por POST
    header("Location: login.php");
    exit;
}
?>
