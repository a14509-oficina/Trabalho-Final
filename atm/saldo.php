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
    <script src="../assets/script.js" defer></script>
</head>
<body class="atm-page">
    <div class="atm-container">
        <div class="atm-machine">
            <div class="atm-machine-inner">
                <div class="atm-left">
                    <div class="atm-brand-bar">
                        <span>🏧</span>
                        <span class="brand-name">DEVBANK</span>
                        <span>MULTIBANCO</span>
                    </div>
                    <div class="atm-screen-area">
                        <div class="atm-side-buttons">
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn"></button>
                        </div>
                        <div class="atm-screen">
                            <div class="atm-screen-inner">
                                <div class="atm-screen-header">
                                    <h1>MULTIBANCO</h1>
                                    <span class="atm-sub">DevBank</span>
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
                                        <a href="menu.php" class="atm-btn">Voltar</a>
                                        <a href="logout.php" class="atm-btn atm-btn-secondary">Sair</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="atm-side-buttons">
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn"></button>
                        </div>
                    </div>
                    <div class="atm-card-reader-area">
                        <span class="atm-slot-label">Cartão</span>
                        <div class="atm-card-slot"></div>
                        <div class="atm-card-reader">
                            <span class="atm-reader-led green"></span>
                            <span class="atm-reader-text">OK</span>
                        </div>
                    </div>
                    <div class="atm-keypad">
                        <button type="button" class="atm-key func-key">Anular</button>
                        <button type="button" class="atm-key func-key">Corrigir</button>
                        <button type="button" class="atm-key func-key">Limpar</button>
                        <button type="button" class="atm-key">7</button>
                        <button type="button" class="atm-key">8</button>
                        <button type="button" class="atm-key">9</button>
                        <button type="button" class="atm-key">4</button>
                        <button type="button" class="atm-key">5</button>
                        <button type="button" class="atm-key">6</button>
                        <button type="button" class="atm-key">1</button>
                        <button type="button" class="atm-key">2</button>
                        <button type="button" class="atm-key">3</button>
                        <button type="button" class="atm-key func-key">±</button>
                        <button type="button" class="atm-key">0</button>
                        <button type="button" class="atm-key confirm-key">OK</button>
                    </div>
                    <div class="atm-slot-row">
                        <div class="atm-cash-slot"></div>
                        <span class="atm-slot-label" style="font-size:7px;">Dinheiro</span>
                        <a href="logout.php" class="atm-btn atm-btn-secondary" style="width:auto;padding:6px 12px;font-size:9px;">Sair</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
