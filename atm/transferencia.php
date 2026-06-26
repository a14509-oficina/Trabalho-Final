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
                    $comprovativo = gerarComprovativo('Transferência', $valor, "Conta destino #$contaDestino", $conta->getSaldo());
                } catch (Exception $e) {
                    $db->rollBack();
                    $erro = 'Erro ao processar transferência: ' . $e->getMessage();
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
    <title>DevBank - Transferência</title>
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
                                        <?= campoCSRF() ?>
                                        <div class="atm-input-group">
                                            <label for="conta_destino">Número da Conta de Destino</label>
                                            <input type="number" id="conta_destino" name="conta_destino" required>
                                        </div>
                                        <div class="atm-input-group">
                                            <label for="valor">Valor (€)</label>
                                            <input type="text" id="valor" name="valor" placeholder="0.00" inputmode="decimal" required>
                                        </div>
                                        <button type="submit" class="atm-btn" data-confirm="Confirmar transferência de €?">Transferir</button>
                                    </form>

                                    <?php if ($comprovativo): ?>
                                        <div style="display:none;" id="comprovativo">
                                            <h2>MULTIBANCO</h2>
                                            <p>DevBank</p>
                                            <div class="linha"></div>
                                            <p><strong>TRANSFERÊNCIA</strong></p>
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
                                        <a href="menu.php" class="atm-btn atm-btn-secondary">Voltar</a>
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
                        <button class="atm-key func-key">Anular</button>
                        <button class="atm-key func-key">Corrigir</button>
                        <button class="atm-key func-key">Limpar</button>
                        <button class="atm-key">7</button>
                        <button class="atm-key">8</button>
                        <button class="atm-key">9</button>
                        <button class="atm-key">4</button>
                        <button class="atm-key">5</button>
                        <button class="atm-key">6</button>
                        <button class="atm-key">1</button>
                        <button class="atm-key">2</button>
                        <button class="atm-key">3</button>
                        <button class="atm-key func-key">±</button>
                        <button class="atm-key">0</button>
                        <button class="atm-key confirm-key">OK</button>
                    </div>
                    <div class="atm-slot-row">
                        <div class="atm-cash-slot"></div>
                        <span class="atm-slot-label" style="font-size:7px;">Dinheiro</span>
                        <a href="logout.php" class="atm-btn atm-btn-secondary" style="width:auto;padding:6px 12px;font-size:9px;">Sair</a>
                    </div>
                </div>
                <div class="atm-right">
                    <div class="atm-right-top">
                        <div class="atm-small-screen">
                            <span class="atm-small-screen-text">Transferência</span>
                        </div>
                        <div class="atm-info-panel">
                            <p class="info-title">Transferir</p>
                            <p>Indique a conta destino e o valor.</p>
                            <p>Não pode transferir para a mesma conta.</p>
                        </div>
                    </div>
                    <div class="atm-right-bottom">
                        <div class="atm-right-slot">
                            <span class="atm-right-slot-label">Pagamentos</span>
                            <div class="atm-card-slot" style="max-width:50px;"></div>
                        </div>
                        <div class="atm-right-slot" style="margin-top:4px;">
                            <span class="atm-right-slot-label">MB WAY</span>
                            <span class="atm-reader-led green"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
