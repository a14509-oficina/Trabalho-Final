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

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valor = str_replace(',', '.', $_POST['valor'] ?? '');
    $valor = (float) $valor;

    if ($valor <= 0) {
        $erro = 'Insira um valor válido.';
    } elseif ($conta->getTipo() === 'poupanca' && $valor > 200) {
        $erro = 'Conta Poupança: levantamento máximo de €200,00 por operação.';
    } else {
        $conta->consultarSaldo();
        if ($conta->debitar($valor)) {
            $conta->registrarTransacao(
                $conta->getId(),
                'levantamento',
                $valor,
                'Levantamento ATM - €' . number_format($valor, 2)
            );
            $mensagem = 'Levantamento de €' . number_format($valor, 2, ',', '.') . ' realizado com sucesso!';
            $conta->consultarSaldo();
        } else {
            $erro = 'Saldo insuficiente ou valor não permitido para este tipo de conta.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Levantamento</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="atm-page">
    <div class="atm-container">
        <div class="atm-screen">
            <div class="atm-header">
                <h1>DevBank</h1>
                <h2>Levantamento</h2>
            </div>
            <div class="atm-body">
                <p class="atm-info">Saldo disponível: <strong>€ <?= number_format($conta->consultarSaldo(), 2, ',', '.') ?></strong></p>

                <?php if ($mensagem): ?>
                    <div class="atm-success"><?= htmlspecialchars($mensagem) ?></div>
                <?php endif; ?>
                <?php if ($erro): ?>
                    <div class="atm-error"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="POST" action="" class="atm-form">
                    <div class="atm-input-group">
                        <label for="valor">Valor a Levantar (€)</label>
                        <input type="text" id="valor" name="valor"
                               placeholder="0.00" inputmode="decimal" required>
                    </div>
                    <button type="submit" class="atm-btn">Levantar</button>
                </form>

                <div class="atm-actions">
                    <a href="menu.php" class="atm-btn atm-btn-secondary">Voltar ao Menu</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
