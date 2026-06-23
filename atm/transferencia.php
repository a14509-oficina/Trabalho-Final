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
    $contaDestino = trim($_POST['conta_destino'] ?? '');
    $valor = str_replace(',', '.', $_POST['valor'] ?? '');
    $valor = (float) $valor;

    if (empty($contaDestino) || $valor <= 0) {
        $erro = 'Preencha todos os campos corretamente.';
    } elseif ($contaDestino == $conta->getId()) {
        $erro = 'Não pode transferir para a mesma conta.';
    } elseif ($conta->getTipo() === 'poupanca' && ($conta->getSaldo() - $valor) < 20) {
        $erro = 'Conta Poupança: o saldo não pode ficar abaixo de €20,00.';
    } else {
        $conta->consultarSaldo();
        if ($conta->getSaldo() < $valor) {
            $erro = 'Saldo insuficiente para esta transferência.';
        } else {
            $db = Database::getConnection();
            try {
                $db->beginTransaction();

                $contaDestinoObj = Conta::buscarPorId((int) $contaDestino);
                if (!$contaDestinoObj) {
                    throw new Exception('Conta de destino não encontrada.');
                }

                if (!$conta->debitar($valor)) {
                    throw new Exception('Valor não permitido para este tipo de conta.');
                }

                $contaDestinoObj->creditar($valor);

                $conta->registrarTransacao(
                    $conta->getId(), 'transferencia', $valor,
                    "Transferência enviada para conta #$contaDestino", (int) $contaDestino
                );

                $contaDestinoObj->registrarTransacao(
                    (int) $contaDestino, 'entrada', $valor,
                    "Transferência recebida da conta #{$conta->getId()}", $conta->getId()
                );

                $db->commit();
                $conta->consultarSaldo();
                $mensagem = 'Transferência de €' . number_format($valor, 2, ',', '.') . ' para conta #' . htmlspecialchars($contaDestino) . ' realizada com sucesso!';
            } catch (Exception $e) {
                $db->rollBack();
                $erro = 'Erro ao processar transferência: ' . $e->getMessage();
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
    <title>DevBank - Transferência</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="atm-page">
    <div class="atm-container">
        <div class="atm-screen">
            <div class="atm-header">
                <h1>DevBank</h1>
                <h2>Transferência</h2>
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
                        <label for="conta_destino">Número da Conta de Destino</label>
                        <input type="number" id="conta_destino" name="conta_destino" required>
                    </div>
                    <div class="atm-input-group">
                        <label for="valor">Valor (€)</label>
                        <input type="text" id="valor" name="valor" placeholder="0.00" inputmode="decimal" required>
                    </div>
                    <button type="submit" class="atm-btn">Transferir</button>
                </form>

                <div class="atm-actions">
                    <a href="menu.php" class="atm-btn atm-btn-secondary">Voltar ao Menu</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
