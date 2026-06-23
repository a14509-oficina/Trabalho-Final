<?php
require_once __DIR__ . '/Database.php';

class Cartao
{
    private int $id;
    private int $contaId;
    private string $numeroCartao;
    private string $pin;
    private bool $ativo;

    public function __construct(int $id, int $contaId, string $numeroCartao, string $pin, bool $ativo)
    {
        $this->id = $id;
        $this->contaId = $contaId;
        $this->numeroCartao = $numeroCartao;
        $this->pin = $pin;
        $this->ativo = $ativo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContaId(): int
    {
        return $this->contaId;
    }

    public function getNumeroCartao(): string
    {
        return $this->numeroCartao;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function validarPin(string $pin): bool
    {
        return password_verify($pin, $this->pin);
    }

    public static function buscarPorNumero(string $numero): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM cartoes WHERE numero_cartao = :numero AND ativo = 1');
        $stmt->execute([':numero' => $numero]);
        $dados = $stmt->fetch();
        if (!$dados) {
            return null;
        }
        return new self(
            (int) $dados['id'],
            (int) $dados['conta_id'],
            $dados['numero_cartao'],
            $dados['pin'],
            (bool) $dados['ativo']
        );
    }
}
