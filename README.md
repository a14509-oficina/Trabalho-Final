# DevBank

Solução bancária modular com Painel Web de Administração e Simulador de Caixa Multibanco.

## Estrutura do Projeto

```
/
├── index.php              # Página inicial (escolha entre Admin e ATM)
├── script.sql             # Base de dados (importar no InfinityFree)
├── config/
│   └── database.php       # Configuração da base de dados
├── classes/
│   ├── Database.php       # Singleton PDO
│   ├── Utilizador.php     # Classe abstrata
│   ├── Admin.php          # Administrador (herda Utilizador)
│   ├── Cliente.php        # Cliente (herda Utilizador)
│   ├── Conta.php          # Conta bancária base
│   ├── ContaCorrente.php  # Conta corrente
│   ├── ContaPoupanca.php  # Conta poupança (final)
│   ├── Cartao.php         # Cartão multibanco
│   └── HistoricoTrait.php # Trait para registo de transações
├── admin/
│   ├── index.php          # Login do administrador
│   ├── dashboard.php      # Dashboard
│   ├── criar_cliente.php  # Criar novo cliente
│   ├── listar_clientes.php# Listar clientes
│   ├── abrir_conta.php    # Abrir conta e emitir cartão
│   └── logout.php
├── atm/
│   ├── index.php          # Inserir cartão
│   ├── validar.php        # Validar cartão/PIN
│   ├── menu.php           # Menu principal
│   ├── saldo.php          # Saldo e movimentos
│   ├── levantamento.php   # Levantamento
│   ├── pagamento.php      # Pagamento de serviços
│   ├── transferencia.php  # Transferências
│   └── logout.php
└── assets/
    └── style.css          # Estilos (Admin + ATM)
```

## Requisitos

- PHP 8.0+
- MySQL 5.7+
- Extensão PDO MySQL

## Configuração

1. Editar `config/database.php` com as credenciais da base de dados
2. Importar `script.sql` no MySQL
3. Aceder via navegador

## Credenciais Padrão

### Administrador
- Email: admin@devbank.com
- Password: admin123

### Cliente de Teste
- Email: joao@email.com
- Password: cliente123
- Cartão: 1234567890123456
- PIN: cliente123

## Funcionalidades POO Implementadas

- **Abstração**: Classe `Utilizador` (abstrata)
- **Herança**: `Admin` e `Cliente` extendem `Utilizador`
- **Polimorfismo**: `ContaCorrente` e `ContaPoupanca` com comportamentos distintos
- **Classe Final**: `ContaPoupanca` (final) com restrições de levantamento
- **Trait**: `HistoricoTrait` reutilizado em `Admin` e `Conta`
- **PDO Prepared Statements**: Todas as queries usam placeholders
- **Transações**: Pagamentos e transferências usam beginTransaction/commit/rollback
