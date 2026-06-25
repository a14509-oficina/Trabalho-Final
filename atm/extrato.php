<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Conta.php';
require_once __DIR__ . '/../classes/helpers.php';

if (!isset($_SESSION['atm_conta_id'])) {
    header('Location: index.php');
    exit;
}

$conta = Conta::buscarPorId($_SESSION['atm_conta_id']);
if (!$conta) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
$dataFim = $_GET['data_fim'] ?? date('Y-m-d');

$db = Database::getConnection();
$stmt = $db->prepare(
    'SELECT * FROM transacoes WHERE conta_id = :conta_id AND DATE(data_hora) BETWEEN :inicio AND :fim ORDER BY data_hora DESC'
);
$stmt->execute([
    ':conta_id' => $conta->getId(),
    ':inicio' => $dataInicio,
    ':fim' => $dataFim,
]);
$movimentos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Extrato</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js" defer></script>
</head>
<body class="atm-page">
    <div class="atm-container">
        <div class="atm-screen">
            <div class="atm-header">
                <h1>MULTIBANCO</h1>
                <span class="atm-sub">DevBank</span>
                <h2>Extrato</h2>
            </div>
            <div class="atm-body">
                <p class="atm-info">
                    Saldo Atual: <strong>€ <?= number_format($conta->consultarSaldo(), 2, ',', '.') ?></strong>
                </p>

                <form method="GET" action="" class="atm-form" style="display:flex;gap:8px;margin-bottom:16px;align-items:end;">
                    <div class="atm-input-group" style="flex:1;margin-bottom:0;">
                        <label for="data_inicio">De</label>
                        <input type="date" id="data_inicio" name="data_inicio" value="<?= $dataInicio ?>" style="font-size:14px;padding:10px;text-align:left;letter-spacing:0;">
                    </div>
                    <div class="atm-input-group" style="flex:1;margin-bottom:0;">
                        <label for="data_fim">Até</label>
                        <input type="date" id="data_fim" name="data_fim" value="<?= $dataFim ?>" style="font-size:14px;padding:10px;text-align:left;letter-spacing:0;">
                    </div>
                    <button type="submit" class="atm-btn" style="width:auto;padding:10px 16px;white-space:nowrap;">Filtrar</button>
                </form>

                <?php if (empty($movimentos)): ?>
                    <p class="atm-empty">Nenhum movimento neste período.</p>
                <?php else: ?>
                    <div style="max-height:300px;overflow-y:auto;">
                        <table class="atm-table">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimentos as $mov): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($mov['data_hora'])) ?></td>
                                        <td><?= ucfirst($mov['tipo']) ?></td>
                                        <td class="<?= $mov['tipo'] === 'entrada' ? 'valor-positivo' : 'valor-negativo' ?>">
                                            <?php if ($mov['tipo'] === 'entrada'): ?>+<?php endif; ?>
                                            € <?= number_format($mov['valor'], 2, ',', '.') ?>
                                        </td>
                                        <td><?= htmlspecialchars($mov['descricao'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="atm-actions">
                    <a href="menu.php" class="atm-btn">Voltar ao Menu</a>
                    <a href="logout.php" class="atm-btn atm-btn-secondary">Sair</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
