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

$mensagem = '';
$erro = '';
$comprovativo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validarTokenCSRF($token)) {
        $erro = 'Sessão inválida.';
    } else {
        $valor = str_replace(',', '.', $_POST['valor'] ?? '');
        $valor = (float) $valor;

        if ($valor <= 0) {
            $erro = 'Insira um valor válido.';
        } elseif ($conta->getTipo() === 'poupanca' && $valor > 200) {
            $erro = 'Conta Poupança: levantamento máximo de €200,00 por operação.';
        } else {
            $conta->consultarSaldo();
            if ($conta->getSaldo() < $valor) {
                $erro = 'Saldo insuficiente.';
            } else {
                $db = Database::getConnection();
                try {
                    $db->beginTransaction();
                    if ($conta->debitar($valor)) {
                        $conta->registrarTransacao(
                            $conta->getId(),
                            'levantamento',
                            $valor,
                            'Levantamento ATM - €' . number_format($valor, 2)
                        );
                        $db->commit();
                        $conta->consultarSaldo();
                        $mensagem = 'Levantamento de €' . number_format($valor, 2, ',', '.') . ' realizado com sucesso!';
                        $comprovativo = gerarComprovativo('Levantamento', $valor, 'Multibanco', $conta->getSaldo());
                    } else {
                        $db->rollBack();
                        $erro = 'Saldo insuficiente ou valor não permitido para este tipo de conta.';
                    }
                } catch (Exception $e) {
                    $db->rollBack();
                    $erro = 'Erro ao processar levantamento.';
                }
            }
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
    <script src="../assets/script.js" defer></script>
</head>
<body class="atm-page">
    <div class="atm-container">
        <div class="atm-screen">
            <div class="atm-header">
                <h1>MULTIBANCO</h1>
                <span class="atm-sub">DevBank</span>
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
                    <?= campoCSRF() ?>
                    <div class="atm-input-group">
                        <label for="valor">Valor a Levantar (€)</label>
                        <input type="text" id="valor" name="valor"
                               placeholder="0.00" inputmode="decimal" required>
                    </div>
                    <button type="submit" class="atm-btn">Levantar</button>
                </form>

                <?php if ($comprovativo): ?>
                    <div style="display:none;" id="comprovativo">
                        <h2>MULTIBANCO</h2>
                        <p>DevBank</p>
                        <div class="linha"></div>
                        <p><strong>LEVANTAMENTO</strong></p>
                        <p class="codigo">Cód: <?= $comprovativo['codigo'] ?></p>
                        <p><?= $comprovativo['data'] ?></p>
                        <p class="valor">€ <?= number_format($comprovativo['valor'], 2, ',', '.') ?></p>
                        <p><?= $comprovativo['descricao'] ?></p>
                        <div class="linha"></div>
                        <p>Saldo Atual: € <?= number_format($comprovativo['saldo_atual'], 2, ',', '.') ?></p>
                        <div class="linha"></div>
                        <p><small>Obrigado por utilizar o Multibanco</small></p>
                    </div>
                    <button onclick="imprimirComprovativo()" class="atm-btn">Imprimir Comprovativo</button>
                <?php endif; ?>

                <div class="atm-actions">
                    <a href="menu.php" class="atm-btn atm-btn-secondary">Voltar ao Menu</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
