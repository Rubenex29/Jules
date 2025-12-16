<?php
// cliente/processa_registo.php

session_start();
require_once '../includes/db.php';

// Apenas processar se o método for POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Obter e limpar os dados do formulário
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $morada = trim($_POST['morada']);
    $password = $_POST['password'];

    // 2. Validação básica
    if (empty($nome) || empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Se a validação falhar, redireciona de volta com uma mensagem de erro
        $_SESSION['error_message'] = "Por favor, preencha todos os campos obrigatórios com dados válidos.";
        header("Location: registo.php");
        exit;
    }

    // 3. Verificar se o email já existe
    try {
        $stmt = $pdo->prepare("SELECT cliente_id FROM cliente WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "O email introduzido já está registado.";
            header("Location: registo.php");
            exit;
        }
    } catch (PDOException $e) {
        die("Erro ao verificar o email: " . $e->getMessage());
    }

    // 4. Hash seguro da password
    $senha_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // 5. Inserir o novo cliente na base de dados
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO cliente (nome, email, telefone, morada, senha_hash, data_registo) 
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        
        $stmt->execute([$nome, $email, $telefone, $morada, $senha_hash]);

        // 6. Redirecionar para a página de login com uma mensagem de sucesso
        $_SESSION['success_message'] = "Conta criada com sucesso! Pode agora fazer login.";
        header("Location: login.php");
        exit;

    } catch (PDOException $e) {
        // Em caso de erro, pode ser logado e uma mensagem genérica exibida
        error_log("Erro no registo: " . $e->getMessage());
        $_SESSION['error_message'] = "Ocorreu um erro ao criar a sua conta. Por favor, tente novamente mais tarde.";
        header("Location: registo.php");
        exit;
    }

} else {
    // Se o acesso não for por POST, redireciona para a página de registo
    header("Location: registo.php");
    exit;
}
?>
