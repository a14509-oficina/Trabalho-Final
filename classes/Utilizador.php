<?php
abstract class Utilizador
{
    protected int $id;
    protected string $nome;
    protected string $email;
    protected string $password;
    protected string $tipo;

    public function __construct(int $id, string $nome, string $email, string $password, string $tipo)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->password = $password;
        $this->tipo = $tipo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function validarPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    abstract public function getRole(): string;
}
