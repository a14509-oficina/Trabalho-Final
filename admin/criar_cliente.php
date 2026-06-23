<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Utilizador.php';
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/HistoricoTrait.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$admin = new Admin($_SESSION['admin_id'], $_SESSION['admin_nome'], '', '', 'admin');
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($nome) && !empty($email) && !empty($password)) {
        try {
            if ($admin->criarCliente($nome, $email, $password)) {
                $mensagem = 'Cliente criado com sucesso!';
            } else {
                $erro = 'Erro ao criar cliente.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro = 'Este email já está registado.';
            } else {
                $erro = 'Erro na base de dados: ' . $e->getMessage();
            }
        }
    } else {
        $erro = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Criar Cliente</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-page">
    <div class="admin-container">
        <div class="admin-header">
            <h1>DevBank</h1>
            <div class="admin-user">
                <span>Bem-vindo, <?= htmlspecialchars($_SESSION['admin_nome']) ?></span>
                <a href="logout.php" class="btn btn-danger">Sair</a>
            </div>
        </div>
        <div class="admin-content">
            <h2>Criar Novo Cliente</h2>
            <?php if ($mensagem): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <form method="POST" action="" class="admin-form">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Palavra-passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Criar Cliente</button>
                    <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
