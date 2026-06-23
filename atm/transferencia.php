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
    } else {
        $conta->consultarSaldo();
        if ($conta->getSaldo() < $valor) {
            $erro = 'Saldo insuficiente para esta transferência.';
        } else {
            $db = Database::getConnection();
            try {
                $db->beginTransaction();

                $stmtDestino = $db->prepare('SELECT id FROM contas WHERE id = :id');
                $stmtDestino->execute([':id' => $contaDestino]);
                $destinoExiste = $stmtDestino->fetch();

                if (!$destinoExiste) {
                    throw new Exception('Conta de destino não encontrada.');
                }

                $stmt = $db->prepare('UPDATE contas SET saldo = saldo - :valor WHERE id = :id');
                $stmt->execute([':valor' => $valor, ':id' => $conta->getId()]);

                $stmt2 = $db->prepare('UPDATE contas SET saldo = saldo + :valor WHERE id = :id');
                $stmt2->execute([':valor' => $valor, ':id' => $contaDestino]);

                $descricao = "Transferência enviada para conta #$contaDestino";
                $stmt3 = $db->prepare(
                    'INSERT INTO transacoes (conta_id, tipo, valor, descricao, conta_destino_id)
                     VALUES (:conta_id, :tipo, :valor, :descricao, :conta_destino_id)'
                );
                $stmt3->execute([
                    ':conta_id' => $conta->getId(),
                    ':tipo' => 'transferencia',
                    ':valor' => $valor,
                    ':descricao' => $descricao,
                    ':conta_destino_id' => (int) $contaDestino,
                ]);

                $descricaoDestino = "Transferência recebida da conta #{$conta->getId()}";
                $stmt4 = $db->prepare(
                    'INSERT INTO transacoes (conta_id, tipo, valor, descricao, conta_destino_id)
                     VALUES (:conta_id, :tipo, :valor, :descricao, :conta_destino_id)'
                );
                $stmt4->execute([
                    ':conta_id' => (int) $contaDestino,
                    ':tipo' => 'entrada',
                    ':valor' => $valor,
                    ':descricao' => $descricaoDestino,
                    ':conta_destino_id' => $conta->getId(),
                ]);

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
