<?php
// admin/processa_alterar_estado.php

session_start();
require_once '../includes/db.php';

// Proteger a página e verificar o método
if (!isset($_SESSION['admin_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit;
}

// 1. Obter e validar os dados do formulário
$encomenda_id = $_POST['encomenda_id'];
$novo_estado_id = $_POST['novo_estado_id'];
$descricao = trim($_POST['descricao']);
$notificar_cliente = isset($_POST['notificar_cliente']) ? 1 : 0;

if (empty($encomenda_id) || empty($novo_estado_id)) {
    // Redireciona com erro se os dados essenciais estiverem em falta
    $_SESSION['admin_error'] = "Dados inválidos para alterar o estado.";
    header("Location: detalhe_encomenda.php?id=" . $encomenda_id);
    exit;
}

// 2. Iniciar transação
$pdo->beginTransaction();

try {
    // 3. Inserir no histórico de estados
    $stmt_historico = $pdo->prepare(
        "INSERT INTO historico_estado (encomenda_id, estado_id, data_estado, descricao, notificar_cliente) 
         VALUES (?, ?, NOW(), ?, ?)"
    );
    $stmt_historico->execute([$encomenda_id, $novo_estado_id, $descricao, $notificar_cliente]);

    // 4. Se notificar_cliente for 1, criar a notificação
    if ($notificar_cliente) {
        // Obter o ID do cliente e o nome do novo estado
        $stmt_info = $pdo->prepare(
            "SELECT e.cliente_id, es.nome_estado 
             FROM encomenda e 
             JOIN estado_encomenda es ON es.estado_id = ?
             WHERE e.encomenda_id = ?"
        );
        $stmt_info->execute([$novo_estado_id, $encomenda_id]);
        $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

        if ($info) {
            $cliente_id = $info['cliente_id'];
            $nome_estado = $info['nome_estado'];
            $conteudo = "O estado da sua encomenda #" . $encomenda_id . " foi atualizado para: " . $nome_estado;

            $stmt_notificacao = $pdo->prepare(
                "INSERT INTO notificacao (cliente_id, encomenda_id, tipo, conteudo, data_envio, estado_envio) 
                 VALUES (?, ?, 'Atualização de Estado', ?, NOW(), 'Pendente')"
            );
            $stmt_notificacao->execute([$cliente_id, $encomenda_id, $conteudo]);
        }
    }

    // 5. Commit da transação
    $pdo->commit();

    $_SESSION['admin_success'] = "Estado da encomenda atualizado com sucesso!";
    header("Location: detalhe_encomenda.php?id=" . $encomenda_id);
    exit;

} catch (PDOException $e) {
    // 6. Em caso de erro, fazer rollback
    // Se ocorrer um erro, a transação é revertida para manter a consistência da base de dados.
    $pdo->rollBack(); 
    
    // É uma boa prática registar o erro real num log de servidor para depuração.
    error_log("Erro ao alterar estado da encomenda #" . $encomenda_id . ": " . $e->getMessage()); 
    
    // Exibe uma mensagem de erro genérica ao utilizador.
    $_SESSION['admin_error'] = "Ocorreu um erro ao atualizar o estado da encomenda. Tente novamente.";
    header("Location: detalhe_encomenda.php?id=" . $encomenda_id);
    exit;
}
?>
