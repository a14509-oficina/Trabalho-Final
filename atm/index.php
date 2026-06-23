<?php
session_start();
$erro = $_SESSION['atm_erro'] ?? '';
unset($_SESSION['atm_erro']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Multibanco</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="atm-page">
    <div class="atm-container">
        <div class="atm-screen">
            <div class="atm-header">
                <h1>DevBank</h1>
                <h2>Caixa Multibanco</h2>
            </div>
            <div class="atm-body">
                <div class="atm-icon">\u0001F3B0</div>
                <p class="atm-instruction">Insira o seu cartão</p>
                <?php if ($erro): ?>
                    <div class="atm-error"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>
                <form method="POST" action="validar.php" class="atm-form">
                    <div class="atm-input-group">
                        <label for="numero_cartao">Número do Cartão</label>
                        <input type="text" id="numero_cartao" name="numero_cartao"
                               maxlength="16" pattern="[0-9]{16}" inputmode="numeric"
                               placeholder="0000 0000 0000 0000" required>
                    </div>
                    <div class="atm-input-group">
                        <label for="pin">PIN</label>
                        <input type="password" id="pin" name="pin"
                               maxlength="4" pattern="[0-9]{4}" inputmode="numeric"
                               placeholder="****" required>
                    </div>
                    <button type="submit" class="atm-btn">Confirmar</button>
                </form>
            </div>
            <div class="atm-footer">
                <a href="../index.php" class="atm-btn atm-btn-secondary">Sair</a>
            </div>
        </div>
    </div>
</body>
</html>
