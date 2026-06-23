<?php
trait HistoricoTrait
{
    public function registrarTransacao(int $contaId, string $tipo, float $valor, string $descricao = '', ?int $contaDestinoId = null): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO transacoes (conta_id, tipo, valor, descricao, conta_destino_id) VALUES (:conta_id, :tipo, :valor, :descricao, :conta_destino_id)'
        );
        return $stmt->execute([
            ':conta_id' => $contaId,
            ':tipo' => $tipo,
            ':valor' => $valor,
            ':descricao' => $descricao,
            ':conta_destino_id' => $contaDestinoId,
        ]);
    }
}
