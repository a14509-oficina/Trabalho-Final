<?php
session_start();
require_once __DIR__ . '/../classes/helpers.php';

$erro = $_SESSION['atm_erro'] ?? '';
unset($_SESSION['atm_erro']);

$bloqueado = false;
$tempoRestante = 0;
if (!verificarRateLimit('atm_login')) {
    $bloqueado = true;
    $tempoRestante = tempoRestanteBloqueio('atm_login');
    $erro = "Cartão bloqueado temporariamente. Tente novamente em $tempoRestante segundos.";
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Multibanco</title>
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
                            <button class="atm-side-btn" onclick="document.getElementById('numero_cartao').focus()"></button>
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn"></button>
                            <button class="atm-side-btn" onclick="document.querySelector('.atm-form button[type=submit]').click()"></button>
                        </div>
                        <div class="atm-screen">
                            <div class="atm-screen-inner">
                                <div class="atm-screen-header">
                                    <h1>MULTIBANCO</h1>
                                    <span class="atm-sub">DevBank</span>
                                    <h2>Caixa Automática</h2>
                                </div>
                                <div class="atm-body">
                                    <div class="atm-icon">🏧</div>
                                    <p class="atm-instruction">Insira o seu cartão</p>
                                    <?php if ($erro): ?>
                                        <div class="atm-error"><?= htmlspecialchars($erro) ?></div>
                                    <?php endif; ?>
                                    <form method="POST" action="validar.php" class="atm-form">
                                        <div class="atm-input-group">
                                            <label for="numero_cartao">Número do Cartão</label>
                                            <input type="text" id="numero_cartao" name="numero_cartao"
                                                   maxlength="19" inputmode="numeric"
                                                   placeholder="0000 0000 0000 0000" autocomplete="off" required>
                                        </div>
                                        <div class="atm-input-group">
                                            <label for="pin">PIN</label>
                                            <input type="password" id="pin" name="pin"
                                                   maxlength="4" pattern="[0-9]{4}" inputmode="numeric"
                                                   placeholder="****" required>
                                        </div>
                                        <?= campoCSRF() ?>
                                        <button type="submit" class="atm-btn" <?= $bloqueado ? 'disabled' : '' ?>>Confirmar</button>
                                    </form>
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
                            <span class="atm-reader-led"></span>
                            <span class="atm-reader-text">Leitor</span>
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
                        <a href="../index.php" class="atm-btn atm-btn-secondary" style="width:auto;padding:6px 12px;font-size:9px;">Sair</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
