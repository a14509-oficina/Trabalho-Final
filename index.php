<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="landing-page">
    <div class="landing-container">
        <h1>DevBank</h1>
        <p>Solução Bancária Modular</p>
        <div class="landing-options">
            <a href="admin/index.php" class="landing-card">
                <span class="landing-card-icon">🔐</span>
                <span class="landing-card-title">Admin</span>
                <span class="landing-card-desc">Painel de Administração</span>
            </a>
            <a href="atm/index.php" class="landing-card">
                <span class="landing-card-icon">🏧</span>
                <span class="landing-card-title">Multibanco</span>
                <span class="landing-card-desc">Caixa Automática</span>
            </a>
        </div>
    </div>
</body>
</html>
