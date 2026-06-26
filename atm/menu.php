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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Menu Multibanco</title>
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
                            <button class="atm-side-btn" onclick="location.href='saldo.php'"></button>
                            <button class="atm-side-btn" onclick="location.href='levantamento.php'"></button>
                            <button class="atm-side-btn" onclick="location.href='pagamento.php'"></button>
                            <button class="atm-side-btn" onclick="location.href='transferencia.php'"></button>
                        </div>
                        <div class="atm-screen">
                            <div class="atm-screen-inner">
                                <div class="atm-screen-header">
                                    <h1>MULTIBANCO</h1>
                                    <span class="atm-sub">DevBank</span>
                                    <h2>Menu Principal</h2>
                                </div>
                                <div class="atm-body">
                                    <p class="atm-welcome">
                                        Cartão •••• •••• •••• <?= substr($_SESSION['atm_cartao_numero'] ?? '', -4) ?>
                                    </p>
                                    <p class="atm-welcome">
                                        Conta <?= ucfirst($conta->getTipo()) ?>
                                    </p>
                                    <div class="atm-menu">
                                        <a href="saldo.php" class="atm-menu-item">
                                            <span class="atm-menu-icon">1</span>
                                            <span>Consultar Saldo</span>
                                        </a>
                                        <a href="levantamento.php" class="atm-menu-item">
                                            <span class="atm-menu-icon">2</span>
                                            <span>Levantamento</span>
                                        </a>
                                        <a href="pagamento.php" class="atm-menu-item">
                                            <span class="atm-menu-icon">3</span>
                                            <span>Pagamento</span>
                                        </a>
                                        <a href="transferencia.php" class="atm-menu-item">
                                            <span class="atm-menu-icon">4</span>
                                            <span>Transferência</span>
                                        </a>
                                        <a href="extrato.php" class="atm-menu-item">
                                            <span class="atm-menu-icon">5</span>
                                            <span>Extrato</span>
                                        </a>
                                        <a href="logout.php" class="atm-menu-item atm-menu-item-full atm-menu-exit">
                                            <span class="atm-menu-icon">0</span>
                                            <span>Sair</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="atm-side-buttons">
                            <button class="atm-side-btn" onclick="location.href='extrato.php'"></button>
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn" onclick="location.href='logout.php'"></button>
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
