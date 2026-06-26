<?php
require_once __DIR__ . '/Utilizador.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/HistoricoTrait.php';

class Admin extends Utilizador
{
    use HistoricoTrait;

    public function getRole(): string
    {
        return 'admin';
    }

    public static function login(string $email, string $password): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM utilizadores WHERE email = :email AND tipo = \'admin\'');
        $stmt->execute([':email' => $email]);
        $dados = $stmt->fetch();

        if ($dados && password_verify($password, $dados['password'])) {
            return new self($dados['id'], $dados['nome'], $dados['email'], $dados['password'], $dados['tipo']);
        }
        return null;
    }

    public function criarCliente(string $nome, string $email, string $password)
    {
        $db = Database::getConnection();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO utilizadores (nome, email, password, tipo) VALUES (:nome, :email, :password, \'cliente\')');
        $ok = $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':password' => $hash,
        ]);
        return $ok ? (int) $db->lastInsertId() : false;
    }

    public static function listarClientes(): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM utilizadores WHERE tipo = 'cliente' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function abrirConta(int $clienteId, string $tipo)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('INSERT INTO contas (cliente_id, tipo, saldo) VALUES (:cliente_id, :tipo, 0.00)');
        $stmt->execute([':cliente_id' => $clienteId, ':tipo' => $tipo]);
        return $db->lastInsertId();
    }

    public static function gerarNumeroCartao(): string
    {
        $db = Database::getConnection();
        do {
            $numero = '';
            for ($i = 0; $i < 16; $i++) {
                $numero .= random_int(0, 9);
            }
            $stmt = $db->prepare('SELECT COUNT(*) FROM cartoes WHERE numero_cartao = :numero');
            $stmt->execute([':numero' => $numero]);
            $existe = $stmt->fetchColumn();
        } while ($existe > 0);

        return $numero;
    }

    public function emitirCartao(int $contaId, string $pin): bool
    {
        $db = Database::getConnection();
        $numero = self::gerarNumeroCartao();
        $pinHash = password_hash($pin, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO cartoes (conta_id, numero_cartao, pin) VALUES (:conta_id, :numero, :pin)');
        return $stmt->execute([
            ':conta_id' => $contaId,
            ':numero' => $numero,
            ':pin' => $pinHash,
        ]);
    }

    public static function obterContasCliente(int $clienteId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM contas WHERE cliente_id = :cliente_id');
        $stmt->execute([':cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }

    public static function obterCartoesConta(int $contaId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM cartoes WHERE conta_id = :conta_id');
        $stmt->execute([':conta_id' => $contaId]);
        return $stmt->fetchAll();
    }
}
