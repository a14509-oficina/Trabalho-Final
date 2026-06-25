# DevBank

Solução bancária modular com Painel Web de Administração e Simulador de Caixa Multibanco.

## Estrutura do Projeto

```
/
├── index.php                # Página inicial (Multibanco)
├── script.sql               # Base de dados
├── .htaccess                # Segurança
├── config/
│   └── database.php         # Configuração da base de dados
├── classes/
│   ├── Database.php         # Singleton PDO
│   ├── Utilizador.php       # Classe abstrata
│   ├── Admin.php            # Administrador
│   ├── Cliente.php          # Cliente
│   ├── Conta.php            # Conta bancária base
│   ├── ContaCorrente.php    # Conta corrente
│   ├── ContaPoupanca.php    # Conta poupança
│   ├── Cartao.php           # Cartão multibanco
│   ├── HistoricoTrait.php   # Trait de transações
│   └── helpers.php          # CSRF, rate limiting, logs
├── admin/
│   ├── index.php            # Login admin
│   ├── dashboard.php        # Dashboard
│   ├── criar_cliente.php    # Criar cliente
│   ├── listar_clientes.php  # Listar clientes
│   ├── abrir_conta.php      # Abrir conta + cartão
│   ├── repor_pin.php        # Repor PIN
│   └── logout.php
├── atm/
│   ├── index.php            # Inserir cartão
│   ├── validar.php          # Validar cartão/PIN
│   ├── menu.php             # Menu principal
│   ├── saldo.php            # Saldo e movimentos
│   ├── levantamento.php     # Levantamento
│   ├── pagamento.php        # Pagamento serviços
│   ├── transferencia.php    # Transferências
│   ├── extrato.php          # Extrato com filtro
│   └── logout.php
└── assets/
    ├── style.css            # Estilos
    └── script.js            # JavaScript
```

## Requisitos

- PHP 8.0+
- MySQL 5.7+
- Extensão PDO MySQL

## Configuração

1. Abrir `config/database.php` e preencher as credenciais da base de dados
2. Executar `php setup/gerar_hashes.php` para gerar as hashes
3. Importar `script.sql` no MySQL
4. Aceder via navegador

## Credenciais Padrão

### Administrador
- Email: admin@devbank.com
- Password: admin123
- URL: `/admin/index.php`

### Cliente de Teste
- Email: joao@email.com
- Password: cliente123

### ATM (Multibanco)
- Cartão: 1234567890123456
- PIN: 1234
- URL: `/atm/index.php`
