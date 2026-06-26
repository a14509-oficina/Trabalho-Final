
CREATE TABLE IF NOT EXISTS utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    tipo ENUM('corrente', 'poupanca') NOT NULL,
    saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES utilizadores(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cartoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conta_id INT NOT NULL,
    numero_cartao VARCHAR(16) NOT NULL UNIQUE,
    pin VARCHAR(255) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conta_id) REFERENCES contas(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conta_id INT NOT NULL,
    tipo ENUM('levantamento', 'pagamento', 'transferencia', 'entrada') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    descricao VARCHAR(255),
    conta_destino_id INT,
    FOREIGN KEY (conta_id) REFERENCES contas(id) ON DELETE CASCADE,
    FOREIGN KEY (conta_destino_id) REFERENCES contas(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS login_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(20) NOT NULL,
    identificador VARCHAR(255) NOT NULL,
    sucesso TINYINT(1) NOT NULL DEFAULT 0,
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dados iniciais (IGNORE para nao duplicar se ja existirem)
INSERT IGNORE INTO utilizadores (nome, email, password, tipo) VALUES
('Administrador', 'admin@devbank.com', '$2y$10$j4YR36F0C2GEY96CpgOnY.aI/cYgl1.dCKp/SJNzIIsfKgfvU3ZOq', 'admin'),
('João Silva', 'joao@email.com', '$2y$10$sd71yeMVYeb6Zy9TwmEpAeopfHJaYMbt4V.82BhoNwW3hbdMxG4Wq', 'cliente');

INSERT IGNORE INTO contas (cliente_id, tipo, saldo) VALUES
(2, 'corrente', 1500.00),
(2, 'poupanca', 5000.00);

INSERT IGNORE INTO cartoes (conta_id, numero_cartao, pin) VALUES
(1, '1234567890123456', '$2y$10$sd71yeMVYeb6Zy9TwmEpAeopfHJaYMbt4V.82BhoNwW3hbdMxG4Wq');
