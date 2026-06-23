<?php
require_once __DIR__ . '/Conta.php';

class ContaCorrente extends Conta
{
    public function debitar(float $valor): bool
    {
        return parent::debitar($valor);
    }
}
