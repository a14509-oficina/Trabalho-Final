<?php
session_start();
require_once __DIR__ . '/classes/Database.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>DevBank - Setup</title>
    <style>
        body { font-family: monospace; padding: 40px; background: #1a2a4a; color: #fff; }
        .ok { color: #2ecc71; }
        .erro { color: #e74c3c; }
        .box { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin: 10px 0; }
        h1 { color: #fff; }
        hr { border: none; border-top: 1px solid rgba(255,255,255,0.2); margin: 20px 0; }
    </style>
</head>
<body>
    <h1>DevBank — Setup</h1>
    <p>A configurar base de dados...</p>
    <hr>

<?php
try {
    $db = Database::getConnection();
    echo '<p class="ok">✅ Ligação à base de dados OK</p>';
} catch (Exception $e) {
    echo '<p class="erro">❌ Erro de ligação: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>Verifica o ficheiro <strong>config/database.php</strong></p>';
    exit;
}

try {
    // Criar tabelas
    $db->exec("CREATE TABLE IF NOT EXISTS utilizadores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        tipo ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo '<p class="ok">✅ Tabela <strong>utilizadores</strong> criada</p>';

    $db->exec("CREATE TABLE IF NOT EXISTS contas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        tipo ENUM('corrente', 'poupanca') NOT NULL,
        saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES utilizadores(id) ON DELETE CASCADE
    )");
    echo '<p class="ok">✅ Tabela <strong>contas</strong> criada</p>';

    $db->exec("CREATE TABLE IF NOT EXISTS cartoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        conta_id INT NOT NULL,
        numero_cartao VARCHAR(16) NOT NULL UNIQUE,
        pin VARCHAR(255) NOT NULL,
        ativo TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (conta_id) REFERENCES contas(id) ON DELETE CASCADE
    )");
    echo '<p class="ok">✅ Tabela <strong>cartoes</strong> criada</p>';

    $db->exec("CREATE TABLE IF NOT EXISTS transacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        conta_id INT NOT NULL,
        tipo ENUM('levantamento', 'pagamento', 'transferencia', 'entrada') NOT NULL,
        valor DECIMAL(10,2) NOT NULL,
        data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        descricao VARCHAR(255),
        conta_destino_id INT,
        FOREIGN KEY (conta_id) REFERENCES contas(id) ON DELETE CASCADE,
        FOREIGN KEY (conta_destino_id) REFERENCES contas(id) ON DELETE SET NULL
    )");
    echo '<p class="ok">✅ Tabela <strong>transacoes</strong> criada</p>';

    $db->exec("CREATE TABLE IF NOT EXISTS login_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tipo VARCHAR(20) NOT NULL,
        identificador VARCHAR(255) NOT NULL,
        sucesso TINYINT(1) NOT NULL DEFAULT 0,
        ip VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo '<p class="ok">✅ Tabela <strong>login_log</strong> criada</p>';

} catch (Exception $e) {
    echo '<p class="erro">❌ Erro ao criar tabelas: ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}

// Inserir dados de seed (se não existirem)
try {
    $stmt = $db->query("SELECT COUNT(*) FROM utilizadores");
    if ($stmt->fetchColumn() == 0) {
        // Gerar hashes
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $clientePass = password_hash('cliente123', PASSWORD_DEFAULT);
        $pin = password_hash('1234', PASSWORD_DEFAULT);

        // Admin
        $db->prepare("INSERT INTO utilizadores (nome, email, password, tipo) VALUES (:nome, :email, :pass, 'admin')")
            ->execute([':nome' => 'Administrador', ':email' => 'admin@devbank.com', ':pass' => $adminPass]);
        echo '<p class="ok">✅ Admin criado (admin@devbank.com / admin123)</p>';

        // Cliente
        $db->prepare("INSERT INTO utilizadores (nome, email, password, tipo) VALUES (:nome, :email, :pass, 'cliente')")
            ->execute([':nome' => 'João Silva', ':email' => 'joao@email.com', ':pass' => $clientePass]);
        echo '<p class="ok">✅ Cliente criado (joao@email.com / cliente123)</p>';

        // Contas
        $db->exec("INSERT INTO contas (cliente_id, tipo, saldo) VALUES (2, 'corrente', 1500.00)");
        $db->exec("INSERT INTO contas (cliente_id, tipo, saldo) VALUES (2, 'poupanca', 5000.00)");
        echo '<p class="ok">✅ Contas criadas (corrente €1500 + poupança €5000)</p>';

        // Cartão
        $db->prepare("INSERT INTO cartoes (conta_id, numero_cartao, pin) VALUES (:conta_id, :numero, :pin)")
            ->execute([':conta_id' => 1, ':numero' => '1234567890123456', ':pin' => $pin]);
        echo '<p class="ok">✅ Cartão criado (1234567890123456 / PIN: 1234)</p>';

        echo '<hr><p class="ok" style="font-size:18px;">✅ Setup completo!</p>';
    } else {
        echo '<hr><p>⚠️ A base de dados já tem dados. Nada foi alterado.</p>';
    }
} catch (Exception $e) {
    echo '<p class="erro">❌ Erro ao inserir dados: ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}
?>

    <hr>
    <p><a href="index.php" style="color:#fff;">➜ Ir para o Multibanco</a></p>
    <p><a href="admin/index.php" style="color:#fff;">➜ Ir para o Admin</a></p>
</body>
</html>
