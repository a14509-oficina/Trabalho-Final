<?php
require_once __DIR__ . '/Conta.php';

final class ContaPoupanca extends Conta
{
    private const MAXIMO_LEVANTAMENTO = 200.00;
    private const SALDO_MINIMO = 20.00;

    public function debitar(float $valor): bool
    {
        if ($valor > self::MAXIMO_LEVANTAMENTO) {
            return false;
        }
        if (($this->saldo - $valor) < self::SALDO_MINIMO) {
            return false;
        }
        return parent::debitar($valor);
    }
}
