<?php
// admin/processa_registo.php

session_start();
require_once '../includes/db.php';

// Apenas processar se o método for POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Obter e limpar os dados
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $permissao = trim($_POST['permissao']);
    $password = $_POST['password'];

    // 2. Validação dos dados
    $permissoes_validas = ['user', 'editor', 'admin'];
    if (empty($nome) || empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($permissao, $permissoes_validas)) {
        $_SESSION['admin_error'] = "Por favor, preencha todos os campos com dados válidos.";
        header("Location: registo.php");
        exit;
    }

    // 3. Verificar se o email já existe na tabela de administradores
    try {
        $stmt = $pdo->prepare("SELECT admin_id FROM admin_user WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['admin_error'] = "O email introduzido já pertence a um administrador.";
            header("Location: registo.php");
            exit;
        }
    } catch (PDOException $e) {
        die("Erro ao verificar o email do administrador: " . $e->getMessage());
    }

    // 4. Hash seguro da password
    $senha_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // 5. Inserir o novo administrador na base de dados
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO admin_user (nome, email, permissao, senha_hash) 
             VALUES (?, ?, ?, ?)"
        );
        
        $stmt->execute([$nome, $email, $permissao, $senha_hash]);

        // 6. Redirecionar para o login com mensagem de sucesso
        $_SESSION['admin_success'] = "Conta de administrador criada com sucesso!";
        header("Location: login.php");
        exit;

    } catch (PDOException $e) {
        error_log("Erro no registo de admin: " . $e->getMessage());
        $_SESSION['admin_error'] = "Ocorreu um erro ao criar a conta de administrador.";
        header("Location: registo.php");
        exit;
    }

} else {
    // Redireciona se o acesso não for por POST
    header("Location: registo.php");
    exit;
}
?>
