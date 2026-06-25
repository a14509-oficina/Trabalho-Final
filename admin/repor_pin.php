<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/helpers.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$mensagem = '';
$erro = '';

$db = Database::getConnection();

// Buscar cartoes para selecao
$stmt = $db->prepare("
    SELECT c.id, c.numero_cartao, u.nome, cc.tipo AS conta_tipo
    FROM cartoes c
    JOIN contas cc ON c.conta_id = cc.id
    JOIN utilizadores u ON cc.cliente_id = u.id
    ORDER BY u.nome
");
$stmt->execute();
$cartoes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validarTokenCSRF($token)) {
        $erro = 'Sessão inválida.';
    } else {
        $cartaoId = (int) ($_POST['cartao_id'] ?? 0);
        $novoPin = $_POST['novo_pin'] ?? '';

        if ($cartaoId <= 0 || !preg_match('/^\d{4}$/', $novoPin)) {
            $erro = 'Selecione um cartão e insira um PIN de 4 dígitos.';
        } else {
            $hash = password_hash($novoPin, PASSWORD_DEFAULT);
            $stmt = $db->prepare('UPDATE cartoes SET pin = :pin WHERE id = :id');
            if ($stmt->execute([':pin' => $hash, ':id' => $cartaoId])) {
                $mensagem = 'PIN atualizado com sucesso!';
            } else {
                $erro = 'Erro ao atualizar PIN.';
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
    <title>DevBank - Repor PIN</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js" defer></script>
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
            <h2>Repor PIN de Cartão</h2>
            <?php if ($mensagem): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <form method="POST" action="" class="admin-form">
                <?= campoCSRF() ?>
                <div class="form-group">
                    <label for="cartao_id">Cartão</label>
                    <select id="cartao_id" name="cartao_id" required>
                        <option value="">Selecione um cartão</option>
                        <?php foreach ($cartoes as $cartao): ?>
                            <option value="<?= $cartao['id'] ?>">
                                **** **** **** <?= substr($cartao['numero_cartao'], -4) ?>
                                — <?= htmlspecialchars($cartao['nome']) ?>
                                (<?= ucfirst($cartao['conta_tipo']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="novo_pin">Novo PIN (4 dígitos)</label>
                    <input type="password" id="novo_pin" name="novo_pin" maxlength="4" pattern="[0-9]{4}" inputmode="numeric" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" data-confirm="Tem a certeza que quer repor o PIN?">Repor PIN</button>
                    <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
