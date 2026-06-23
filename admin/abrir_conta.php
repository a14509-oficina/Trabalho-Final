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

$db = Database::getConnection();
$stmt = $db->prepare("SELECT id, nome, email FROM utilizadores WHERE tipo = :tipo ORDER BY nome");
$stmt->execute([':tipo' => 'cliente']);
$clientes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteId = (int) ($_POST['cliente_id'] ?? 0);
    $tipoConta = $_POST['tipo_conta'] ?? '';
    $pin = $_POST['pin'] ?? '';

    if ($clienteId <= 0 || !in_array($tipoConta, ['corrente', 'poupanca'])) {
        $erro = 'Selecione um cliente e um tipo de conta válido.';
    } elseif (!preg_match('/^\d{4}$/', $pin)) {
        $erro = 'O PIN deve ter exatamente 4 dígitos numéricos.';
    } else {
        try {
            $db->beginTransaction();

            $contaId = $admin->abrirConta($clienteId, $tipoConta);
            if ($contaId === false) {
                throw new Exception('Erro ao criar conta.');
            }

            if ($admin->emitirCartao((int) $contaId, $pin)) {
                $stmt = $db->prepare('SELECT numero_cartao FROM cartoes WHERE conta_id = :conta_id ORDER BY id DESC LIMIT 1');
                $stmt->execute([':conta_id' => $contaId]);
                $cartao = $stmt->fetch();

                $db->commit();
                $mensagem = 'Conta ' . ucfirst($tipoConta) . ' criada com sucesso! '
                    . 'Número do Cartão: ' . ($cartao['numero_cartao'] ?? 'N/A');
            } else {
                $db->rollBack();
                $erro = 'Erro ao emitir cartão.';
            }
        } catch (Exception $e) {
            $db->rollBack();
            $erro = 'Erro: ' . $e->getMessage();
        }
    }
}

$clienteSelecionado = (int) ($_GET['cliente_id'] ?? $_POST['cliente_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBank - Abrir Conta</title>
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
            <h2>Abrir Conta e Emitir Cartão</h2>
            <?php if ($mensagem): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <form method="POST" action="" class="admin-form">
                <div class="form-group">
                    <label for="cliente_id">Cliente</label>
                    <select id="cliente_id" name="cliente_id" required>
                        <option value="">Selecione um cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id'] ?>" <?= $cliente['id'] === $clienteSelecionado ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cliente['nome']) ?> (<?= htmlspecialchars($cliente['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tipo_conta">Tipo de Conta</label>
                    <select id="tipo_conta" name="tipo_conta" required>
                        <option value="">Selecione o tipo</option>
                        <option value="corrente">Conta Corrente</option>
                        <option value="poupanca">Conta Poupança</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="pin">PIN do Cartão (4 dígitos)</label>
                    <input type="password" id="pin" name="pin" maxlength="4" pattern="[0-9]{4}" inputmode="numeric" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Criar Conta e Cartão</button>
                    <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
