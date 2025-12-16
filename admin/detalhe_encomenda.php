<?php
// admin/detalhe_encomenda.php

session_start();
require_once '../includes/db.php';

// Proteger a página
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$encomenda_id = $_GET['id'] ?? null;
if (!$encomenda_id) {
    header("Location: gestao_encomendas.php");
    exit;
}

// Lógica para ir buscar os detalhes da encomenda e estados possíveis
try {
    // Detalhes da encomenda e do cliente
    $sql_encomenda = "
        SELECT e.*, c.nome AS cliente_nome, c.email, c.telefone, c.morada
        FROM encomenda AS e
        JOIN cliente AS c ON e.cliente_id = c.cliente_id
        WHERE e.encomenda_id = ?";
    $stmt_encomenda = $pdo->prepare($sql_encomenda);
    $stmt_encomenda->execute([$encomenda_id]);
    $encomenda = $stmt_encomenda->fetch(PDO::FETCH_ASSOC);

    // Itens da encomenda
    $sql_itens = "
        SELECT i.*, p.nome AS produto_nome
        FROM encomenda_item AS i
        JOIN produto AS p ON i.produto_id = p.produto_id
        WHERE i.encomenda_id = ?";
    $stmt_itens = $pdo->prepare($sql_itens);
    $stmt_itens->execute([$encomenda_id]);
    $itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

    // Histórico de estados
    $sql_historico = "
        SELECT h.data_estado, s.nome_estado, h.descricao
        FROM historico_estado AS h
        JOIN estado_encomenda AS s ON h.estado_id = s.estado_id
        WHERE h.encomenda_id = ?
        ORDER BY h.data_estado DESC";
    $stmt_historico = $pdo->prepare($sql_historico);
    $stmt_historico->execute([$encomenda_id]);
    $historico = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);
    
    // Obter o estado atual
    $estado_atual = $historico[0]['nome_estado'] ?? 'N/A';

    // Obter todos os estados possíveis para o formulário
    $estados_possiveis = $pdo->query("SELECT estado_id, nome_estado FROM estado_encomenda ORDER BY ordem")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar os detalhes da encomenda: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhe da Encomenda #<?php echo $encomenda_id; ?> - CRM Admin</title>
    <link rel="stylesheet" href="../public/css/admin_panel.css">
    <link rel="stylesheet" href="../public/css/detalhe.css">
</head>
<body>
    <div class="admin-panel-container">
        <aside class="sidebar">
            <h3>CRM Admin</h3>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="gestao_clientes.php">Gestão de Clientes</a>
                <a href="gestao_encomendas.php" class="active">Gestão de Encomendas</a>
                <a href="logout.php">Sair</a>
            </nav>
        </aside>
        <main class="content">
            <header>
                <h2>Detalhe da Encomenda #<?php echo $encomenda_id; ?></h2>
                <p>Estado Atual: <strong><?php echo htmlspecialchars($estado_atual); ?></strong></p>
            </header>

            <section class="grid-container">
                <div class="card">
                    <h4>Cliente</h4>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($encomenda['cliente_nome']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($encomenda['email']); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($encomenda['telefone']); ?></p>
                    <p><strong>Morada de Entrega:</strong> <?php echo htmlspecialchars($encomenda['endereco_entrega']); ?></p>
                </div>
                
                <div class="card">
                    <h4>Alterar Estado</h4>
                    <form action="processa_alterar_estado.php" method="POST">
                        <input type="hidden" name="encomenda_id" value="<?php echo $encomenda_id; ?>">
                        <div class="form-group">
                            <label for="novo_estado">Novo Estado:</label>
                            <select name="novo_estado_id" id="novo_estado" required>
                                <?php foreach ($estados_possiveis as $estado): ?>
                                    <option value="<?php echo $estado['estado_id']; ?>"><?php echo htmlspecialchars($estado['nome_estado']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="descricao">Descrição/Observações:</label>
                            <textarea name="descricao" id="descricao" rows="2"></textarea>
                        </div>
                        <div class="form-group notify">
                            <input type="checkbox" name="notificar_cliente" id="notificar_cliente" value="1">
                            <label for="notificar_cliente">Notificar Cliente</label>
                        </div>
                        <button type="submit">Atualizar Estado</button>
                    </form>
                </div>

                <div class="card full-width">
                    <h4>Itens da Encomenda</h4>
                    <ul>
                        <?php foreach ($itens as $item): ?>
                            <li>
                                <?php echo $item['quantidade']; ?>x <?php echo htmlspecialchars($item['produto_nome']); ?> 
                                (Preço Un.: €<?php echo number_format($item['preco_unitario'], 2); ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p><strong>Valor Total:</strong> €<?php echo number_format($encomenda['valor_total'], 2); ?></p>
                </div>

                <div class="card full-width">
                    <h4>Histórico de Estados</h4>
                    <ul>
                        <?php foreach ($historico as $h): ?>
                            <li>
                                <strong><?php echo date("d/m/Y H:i", strtotime($h['data_estado'])); ?> - <?php echo htmlspecialchars($h['nome_estado']); ?></strong>
                                <p><?php echo htmlspecialchars($h['descricao']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
