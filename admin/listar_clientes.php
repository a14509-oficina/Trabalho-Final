<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$clientes = Admin::listarClientes();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Listar Clientes</title>
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
            <h2>Lista de Clientes</h2>
            <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Data de Registo</th>
                            <th>Contas</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientes)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhum cliente registado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><?= $cliente['id'] ?></td>
                                    <td><?= htmlspecialchars($cliente['nome']) ?></td>
                                    <td><?= htmlspecialchars($cliente['email']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($cliente['created_at'])) ?></td>
                                    <td>
                                        <?php
                                        $contas = Admin::obterContasCliente($cliente['id']);
                                        foreach ($contas as $conta) {
                                            $cartoes = Admin::obterCartoesConta($conta['id']);
                                            $numCartoes = count($cartoes);
                                            echo htmlspecialchars(ucfirst($conta['tipo']))
                                                . ' (Saldo: €' . number_format($conta['saldo'], 2) . ')'
                                                . ' - ' . $numCartoes . ' cartão(ões)<br>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="abrir_conta.php?cliente_id=<?= $cliente['id'] ?>" class="btn btn-sm btn-primary">Abrir Conta</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
