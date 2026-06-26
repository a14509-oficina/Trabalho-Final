<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/HistoricoTrait.php';

class Conta
{
    use HistoricoTrait;

    protected int $id;
    protected int $clienteId;
    protected string $tipo;
    protected float $saldo;

    public function __construct(int $id, int $clienteId, string $tipo, float $saldo)
    {
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->tipo = $tipo;
        $this->saldo = $saldo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClienteId(): int
    {
        return $this->clienteId;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getSaldo(): float
    {
        return $this->saldo;
    }

    public function consultarSaldo(): float
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT saldo FROM contas WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
        $this->saldo = (float) $stmt->fetchColumn();
        return $this->saldo;
    }

    public function debitar(float $valor): bool
    {
        if ($valor <= 0) {
            throw new InvalidArgumentException('Valor inválido para débito.');
        }
        if ($this->saldo < $valor) {
            return false;
        }
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE contas SET saldo = saldo - :valor WHERE id = :id');
        $resultado = $stmt->execute([':valor' => $valor, ':id' => $this->id]);
        if ($resultado) {
            $this->saldo -= $valor;
        }
        return $resultado;
    }

    public function creditar(float $valor): bool
    {
        if ($valor <= 0) {
            throw new InvalidArgumentException('Valor inválido para crédito.');
        }
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE contas SET saldo = saldo + :valor WHERE id = :id');
        $resultado = $stmt->execute([':valor' => $valor, ':id' => $this->id]);
        if ($resultado) {
            $this->saldo += $valor;
        }
        return $resultado;
    }

    public function getUltimosMovimentos(int $limite = 5): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT * FROM transacoes WHERE conta_id = :conta_id ORDER BY data_hora DESC LIMIT :limite'
        );
        $stmt->bindValue(':conta_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM contas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch();
        if (!$dados) {
            return null;
        }
        return self::criarInstancia($dados);
    }

    public static function buscarPorClienteEId(int $clienteId, int $contaId): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM contas WHERE id = :id AND cliente_id = :cliente_id');
        $stmt->execute([':id' => $contaId, ':cliente_id' => $clienteId]);
        $dados = $stmt->fetch();
        if (!$dados) {
            return null;
        }
        return self::criarInstancia($dados);
    }

    public static function buscarPorNumeroConta(string $numero): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM contas WHERE id = :id');
        $stmt->execute([':id' => $numero]);
        $dados = $stmt->fetch();
        if (!$dados) {
            return null;
        }
        return self::criarInstancia($dados);
    }

    protected static function criarInstancia(array $dados): self
    {
        if ($dados['tipo'] === 'poupanca') {
            require_once __DIR__ . '/ContaPoupanca.php';
            return new ContaPoupanca($dados['id'], $dados['cliente_id'], $dados['tipo'], (float) $dados['saldo']);
        }
        require_once __DIR__ . '/ContaCorrente.php';
        return new ContaCorrente($dados['id'], $dados['cliente_id'], $dados['tipo'], (float) $dados['saldo']);
    }
}
