<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Conta.php';
require_once __DIR__ . '/../classes/ContaCorrente.php';
require_once __DIR__ . '/../classes/ContaPoupanca.php';

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
</head>
<body class="atm-page">
    <div class="atm-container">
        <div class="atm-screen">
            <div class="atm-header">
                <h1>DevBank</h1>
                <h2>Menu Principal</h2>
            </div>
            <div class="atm-body">
                <p class="atm-welcome">
                    Cartão: **** **** **** <?= substr($_SESSION['atm_cartao_numero'] ?? '', -4) ?>
                </p>
                <p class="atm-welcome">
                    Conta: <?= ucfirst($conta->getTipo()) ?>
                </p>
                <div class="atm-menu">
                    <a href="saldo.php" class="atm-menu-item">
                        <span class="atm-menu-icon">💰</span>
                        <span>Consultar Saldo</span>
                    </a>
                    <a href="levantamento.php" class="atm-menu-item">
                        <span class="atm-menu-icon">💸</span>
                        <span>Levantamento</span>
                    </a>
                    <a href="pagamento.php" class="atm-menu-item">
                        <span class="atm-menu-icon">📄</span>
                        <span>Pagamento de Serviços</span>
                    </a>
                    <a href="transferencia.php" class="atm-menu-item">
                        <span class="atm-menu-icon">🔄</span>
                        <span>Transferência</span>
                    </a>
                    <a href="logout.php" class="atm-menu-item atm-menu-exit">
                        <span class="atm-menu-icon">🚪</span>
                        <span>Sair</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
