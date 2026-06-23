<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Dashboard</title>
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
        <div class="admin-nav">
            <a href="criar_cliente.php" class="nav-card">
                <span class="nav-icon">+</span>
                <span>Criar Cliente</span>
            </a>
            <a href="listar_clientes.php" class="nav-card">
                <span class="nav-icon">📋</span>
                <span>Listar Clientes</span>
            </a>
            <a href="abrir_conta.php" class="nav-card">
                <span class="nav-icon">💳</span>
                <span>Abrir Conta / Emitir Cartão</span>
            </a>
        </div>
    </div>
</body>
</html>
