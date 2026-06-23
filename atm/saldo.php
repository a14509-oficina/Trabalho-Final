<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Conta.php';
require_once __DIR__ . '/../classes/ContaCorrente.php';
require_once __DIR__ . '/../classes/ContaPoupanca.php';
require_once __DIR__ . '/../classes/HistoricoTrait.php';

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

$saldo = $conta->consultarSaldo();
$movimentos = $conta->getUltimosMovimentos(5);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Saldo e Movimentos</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="atm-page">
    <div class="atm-container">
        <div class="atm-screen">
            <div class="atm-header">
                <h1>DevBank</h1>
                <h2>Saldo e Movimentos</h2>
            </div>
            <div class="atm-body">
                <div class="atm-saldo-box">
                    <p class="atm-saldo-label">Saldo Atual</p>
                    <p class="atm-saldo-value">€ <?= number_format($saldo, 2, ',', '.') ?></p>
                </div>

                <div class="atm-movimentos">
                    <h3>Últimos Movimentos</h3>
                    <?php if (empty($movimentos)): ?>
                        <p class="atm-empty">Nenhum movimento registado.</p>
                    <?php else: ?>
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
                                        <td class="<?= in_array($mov['tipo'], ['entrada']) ? 'valor-positivo' : 'valor-negativo' ?>">
                                            <?php if (in_array($mov['tipo'], ['entrada'])): ?>+<?php endif; ?>
                                            € <?= number_format($mov['valor'], 2, ',', '.') ?>
                                        </td>
                                        <td><?= htmlspecialchars($mov['descricao'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <div class="atm-actions">
                    <a href="menu.php" class="atm-btn">Voltar ao Menu</a>
                    <a href="logout.php" class="atm-btn atm-btn-secondary">Sair</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
