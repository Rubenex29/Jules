<?php
// cliente/detalhe_encomenda.php

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
$encomenda_id = $_GET['id'] ?? null;

if (!$encomenda_id) {
    header("Location: painel.php");
    exit;
}

// Lógica para ir buscar os detalhes da encomenda, garantindo que pertence ao cliente
try {
    // Detalhes da encomenda
    $stmt_encomenda = $pdo->prepare("SELECT * FROM encomenda WHERE encomenda_id = ? AND cliente_id = ?");
    $stmt_encomenda->execute([$encomenda_id, $cliente_id]);
    $encomenda = $stmt_encomenda->fetch(PDO::FETCH_ASSOC);

    // Se a encomenda não for encontrada ou não pertencer ao cliente, redireciona
    if (!$encomenda) {
        header("Location: painel.php");
        exit;
    }

    // Itens da encomenda
    $stmt_itens = $pdo->prepare("
        SELECT i.quantidade, i.preco_unitario, p.nome AS produto_nome
        FROM encomenda_item i
        JOIN produto p ON i.produto_id = p.produto_id
        WHERE i.encomenda_id = ?");
    $stmt_itens->execute([$encomenda_id]);
    $itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

    // Histórico de estados
    $stmt_historico = $pdo->prepare("
        SELECT h.data_estado, s.nome_estado, h.descricao
        FROM historico_estado h
        JOIN estado_encomenda s ON h.estado_id = s.estado_id
        WHERE h.encomenda_id = ?
        ORDER BY h.data_estado DESC");
    $stmt_historico->execute([$encomenda_id]);
    $historico = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar os detalhes da encomenda: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhe da Encomenda - CRM</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/painel.css">
    <link rel="stylesheet" href="../public/css/detalhe_cliente.css">
</head>
<body>
    <div class="painel-container">
        <header class="painel-header">
            <h1>Detalhes da Encomenda #<?php echo htmlspecialchars($encomenda_id); ?></h1>
            <a href="painel.php" class="logout-btn">Voltar ao Painel</a>
        </header>

        <section class="encomenda-info">
            <div class="info-item">
                <strong>Data da Encomenda:</strong>
                <p><?php echo date("d/m/Y", strtotime($encomenda['data_criacao'])); ?></p>
            </div>
            <div class="info-item">
                <strong>Endereço de Entrega:</strong>
                <p><?php echo htmlspecialchars($encomenda['endereco_entrega']); ?></p>
            </div>
            <div class="info-item">
                <strong>Valor Total:</strong>
                <p>€<?php echo number_format($encomenda['valor_total'], 2, ',', '.'); ?></p>
            </div>
        </section>

        <section class="encomenda-itens">
            <h2>Itens da Encomenda</h2>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['produto_nome']); ?></td>
                            <td><?php echo $item['quantidade']; ?></td>
                            <td>€<?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                            <td>€<?php echo number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="historico-estados">
            <h2>Histórico da Encomenda</h2>
            <ul>
                <?php foreach ($historico as $h): ?>
                    <li>
                        <span class="data"><?php echo date("d/m/Y H:i", strtotime($h['data_estado'])); ?></span>
                        <span class="estado"><?php echo htmlspecialchars($h['nome_estado']); ?></span>
                        <p class="descricao"><?php echo htmlspecialchars($h['descricao']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
</body>
</html>
