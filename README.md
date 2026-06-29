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

## Configuração (InfinityFree)

1. Fazer upload de todos os ficheiros para o servidor
2. No phpMyAdmin da InfinityFree, importar o ficheiro `script.sql`
3. (Alternativa) Aceder a `/setup.php` no navegador para criar tabelas + dados automaticamente
4. Aceder ao site

## Credenciais Padrão

### Administrador
| Campo | Valor |
|-------|-------|
| URL | `/admin/index.php` |
| Email | `admin@devbank.com` |
| Password | `admin123` |

### Cliente de Teste (web)
| Campo | Valor |
|-------|-------|
| URL | `/admin/index.php` (gerido pelo admin) |
| Email | `joao@email.com` |
| Password | `cliente123` |

### ATM / Multibanco
| Campo | Valor |
|-------|-------|
| URL | `/atm/index.php` |
| Cartão 1 (João Silva) | `1234567890123456` |
| Cartão 2 (Ana Silva) | `1029899247927652` |
| Cartão 3 (Rui Santos) | `9112416668034772` |
| PIN (todos) | `1234` |
