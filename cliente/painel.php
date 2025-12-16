<?php
// cliente/painel.php

session_start();
require_once '../includes/db.php';

// 1. Proteger a página: verificar se o cliente está autenticado
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// 2. Ir buscar os dados do cliente à base de dados
try {
    $stmt_cliente = $pdo->prepare("SELECT nome, email, telefone, morada, data_registo FROM cliente WHERE cliente_id = ?");
    $stmt_cliente->execute([$cliente_id]);
    $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        // Se o cliente não for encontrado, destrói a sessão e redireciona
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // 3. Ir buscar o histórico de encomendas do cliente
    // A query para obter o estado mais recente de cada encomenda é um pouco complexa.
    // Primeiro, encontramos a data mais recente no histórico para cada encomenda (subquery `ultimo_estado`).
    // Depois, juntamos essa informação de volta à tabela de histórico para obter o ID do estado correspondente.
    // Finalmente, juntamos com a tabela de estados para obter o nome do estado.
    $sql_encomendas = "
        SELECT 
            e.encomenda_id, 
            e.data_criacao, 
            e.valor_total,
            est.nome_estado AS estado_atual
        FROM encomenda AS e
        JOIN (
            SELECT he.encomenda_id, MAX(he.data_estado) AS max_data
            FROM historico_estado AS he
            GROUP BY he.encomenda_id
        ) AS ultimo_estado ON e.encomenda_id = ultimo_estado.encomenda_id
        JOIN historico_estado AS he_atual ON ultimo_estado.encomenda_id = he_atual.encomenda_id AND ultimo_estado.max_data = he_atual.data_estado
        JOIN estado_encomenda AS est ON he_atual.estado_id = est.estado_id
        WHERE e.cliente_id = ?
        ORDER BY e.data_criacao DESC";
        
    $stmt_encomendas = $pdo->prepare($sql_encomendas);
    $stmt_encomendas->execute([$cliente_id]);
    $encomendas = $stmt_encomendas->fetchAll(PDO::FETCH_ASSOC);

    // Contar notificações não lidas
    $stmt_not_count = $pdo->prepare("SELECT COUNT(*) FROM notificacao WHERE cliente_id = ? AND estado_envio != 'Lida'");
    $stmt_not_count->execute([$cliente_id]);
    $notificacoes_nao_lidas = $stmt_not_count->fetchColumn();

} catch (PDOException $e) {
    die("Erro ao carregar os dados do painel: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Cliente - CRM</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/painel.css">
</head>
<body>
    <div class="painel-container">
        <header class="painel-header">
            <h1>Bem-vindo, <?php echo htmlspecialchars($cliente['nome']); ?>!</h1>
            <div class="header-actions">
                <a href="notificacoes.php" class="notificacoes-link">
                    Notificações 
                    <?php if ($notificacoes_nao_lidas > 0): ?>
                        <span class="badge"><?php echo $notificacoes_nao_lidas; ?></span>
                    <?php endif; ?>
                </a>
                <a href="logout.php" class="logout-btn">Sair</a>
            </div>
        </header>

        <section class="dados-pessoais">
            <h2>Os Seus Dados</h2>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($cliente['nome']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($cliente['telefone'] ?? 'Não definido'); ?></p>
            <p><strong>Morada:</strong> <?php echo htmlspecialchars($cliente['morada'] ?? 'Não definida'); ?></p>
            <p><strong>Cliente desde:</strong> <?php echo date("d/m/Y", strtotime($cliente['data_registo'])); ?></p>
            <a href="editar_dados.php" class="btn-editar">Editar Dados</a>
        </section>

        <section class="historico-encomendas">
            <h2>Histórico de Encomendas</h2>
            <?php if (count($encomendas) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Encomenda</th>
                            <th>Data</th>
                            <th>Valor Total</th>
                            <th>Estado Atual</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($encomendas as $encomenda): ?>
                            <tr>
                                <td>#<?php echo $encomenda['encomenda_id']; ?></td>
                                <td><?php echo date("d/m/Y", strtotime($encomenda['data_criacao'])); ?></td>
                                <td>€<?php echo number_format($encomenda['valor_total'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($encomenda['estado_atual']); ?></td>
                                <td><a href="detalhe_encomenda.php?id=<?php echo $encomenda['encomenda_id']; ?>" class="btn-ver">Ver Detalhes</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Ainda não efetuou nenhuma encomenda.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
