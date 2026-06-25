<?php
// Gera os hashes bcrypt para os dados de seed
// Uso: php setup/gerar_hashes.php

$adminPass = password_hash('admin123', PASSWORD_DEFAULT);
$clientePass = password_hash('cliente123', PASSWORD_DEFAULT);
$pin = password_hash('1234', PASSWORD_DEFAULT);

echo "Admin password hash: $adminPass\n";
echo "Cliente password hash: $clientePass\n";
echo "PIN (1234) hash: $pin\n\n";

echo "--- SQL para inserir ---\n\n";

echo "-- Admin\n";
echo "INSERT INTO utilizadores (nome, email, password, tipo) VALUES ('Administrador', 'admin@devbank.com', '$adminPass', 'admin');\n\n";

echo "-- Cliente\n";
echo "INSERT INTO utilizadores (nome, email, password, tipo) VALUES ('João Silva', 'joao@email.com', '$clientePass', 'cliente');\n\n";

echo "-- Contas\n";
echo "INSERT INTO contas (cliente_id, tipo, saldo) VALUES (2, 'corrente', 1500.00);\n";
echo "INSERT INTO contas (cliente_id, tipo, saldo) VALUES (2, 'poupanca', 5000.00);\n\n";

echo "-- Cartao (PIN 1234)\n";
echo "INSERT INTO cartoes (conta_id, numero_cartao, pin) VALUES (1, '1234567890123456', '$pin');\n";
