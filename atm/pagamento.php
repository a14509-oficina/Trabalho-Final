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
    $entidade = trim($_POST['entidade'] ?? '');
    $referencia = trim($_POST['referencia'] ?? '');
    $valor = str_replace(',', '.', $_POST['valor'] ?? '');
    $valor = (float) $valor;

    if (empty($entidade) || empty($referencia) || $valor <= 0) {
        $erro = 'Preencha todos os campos corretamente.';
    } elseif ($conta->getTipo() === 'poupanca' && ($conta->getSaldo() - $valor) < 20) {
        $erro = 'Conta Poupança: o saldo não pode ficar abaixo de €20,00.';
    } else {
        $conta->consultarSaldo();
        if ($conta->getSaldo() < $valor) {
            $erro = 'Saldo insuficiente para este pagamento.';
        } else {
            $db = Database::getConnection();
            try {
                $db->beginTransaction();

                if (!$conta->debitar($valor)) {
                    throw new Exception('Valor não permitido para este tipo de conta.');
                }

                $descricao = "Pagamento - Entidade: $entidade / Ref: $referencia";
                $conta->registrarTransacao($conta->getId(), 'pagamento', $valor, $descricao);

                $db->commit();
                $conta->consultarSaldo();
                $mensagem = 'Pagamento de €' . number_format($valor, 2, ',', '.') . ' realizado com sucesso!';
            } catch (Exception $e) {
                $db->rollBack();
                $erro = 'Erro ao processar pagamento: ' . $e->getMessage();
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
    <title>DevBank - Pagamento de Serviços</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="atm-page">
    <div class="atm-container">
        <div class="atm-screen">
            <div class="atm-header">
                <h1>DevBank</h1>
                <h2>Pagamento de Serviços</h2>
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
                        <label for="entidade">Entidade</label>
                        <input type="text" id="entidade" name="entidade" maxlength="5" required>
                    </div>
                    <div class="atm-input-group">
                        <label for="referencia">Referência</label>
                        <input type="text" id="referencia" name="referencia" maxlength="15" required>
                    </div>
                    <div class="atm-input-group">
                        <label for="valor">Valor (€)</label>
                        <input type="text" id="valor" name="valor" placeholder="0.00" inputmode="decimal" required>
                    </div>
                    <button type="submit" class="atm-btn">Pagar</button>
                </form>

                <div class="atm-actions">
                    <a href="menu.php" class="atm-btn atm-btn-secondary">Voltar ao Menu</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
