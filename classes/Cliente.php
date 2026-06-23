<?php
require_once __DIR__ . '/Utilizador.php';
require_once __DIR__ . '/Database.php';

class Cliente extends Utilizador
{
    public function getRole(): string
    {
        return 'cliente';
    }

    public function getContas(): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM contas WHERE cliente_id = :cliente_id');
        $stmt->execute([':cliente_id' => $this->id]);
        return $stmt->fetchAll();
    }
}
