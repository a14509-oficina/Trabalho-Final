<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/helpers.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validarTokenCSRF($token)) {
        $erro = 'Sessão inválida. Tente novamente.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!empty($email) && !empty($password)) {
            if (!verificarRateLimit('admin_login_' . $email)) {
                $restante = tempoRestanteBloqueio('admin_login_' . $email);
                $erro = "Demasiadas tentativas. Tente novamente em $restante segundos.";
            } else {
                $admin = Admin::login($email, $password);
                registarLogAcesso('admin', $email, $admin !== null);
                if ($admin) {
                    $_SESSION['admin_id'] = $admin->getId();
                    $_SESSION['admin_nome'] = $admin->getNome();
                    unset($_SESSION['admin_login_' . $email]);
                    header('Location: dashboard.php');
                    exit;
                } else {
                    registrarTentativa('admin_login_' . $email);
                    $erro = 'Email ou palavra-passe inválidos.';
                }
            }
        } else {
            $erro = 'Preencha todos os campos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Admin Login</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js" defer></script>
</head>
<body class="admin-page">
    <div class="login-container">
        <div class="login-card">
            <h1>DevBank</h1>
            <h2>Painel de Administração</h2>
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <?= campoCSRF() ?>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Palavra-passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
                <div style="text-align:center;margin-top:20px;">
                    <a href="../index.php" style="color:#999;text-decoration:none;font-size:12px;">← Voltar ao Multibanco</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
