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

## Como Testar Localmente (Amanhã)

### Opção 1: XAMPP (Windows) — Recomendado

1. **Instalar XAMPP**: https://www.apachefriends.org/
2. **Mover o projeto**:
   ```bash
   # Copiar a pasta Trabalho-Final para o htdocs do XAMPP
   # Exemplo: C:\xampp\htdocs\Trabalho-Final
   ```
3. **Iniciar servidores**: Abrir XAMPP Control Panel → Start **Apache** e **MySQL**
4. **Criar a base de dados**:
   - Abrir http://localhost/phpmyadmin
   - Clicar em "Novo" → Nome: `devbank` → Criar
   - Clicar em "SQL" e colar o conteúdo do `script.sql` → Executar
5. **Configurar acesso**:
   - Abrir `config/database.php`
   - Em XAMPP padrão: user = `root`, pass = `""` (vazio) — já está configurado
6. **Aceder**: http://localhost/Trabalho-Final

### Opção 2: Linux com PHP nativo

```bash
# 1. Instalar PHP e MySQL
sudo apt install php php-mysql mysql-server

# 2. Iniciar MySQL e criar base de dados
sudo systemctl start mysql
sudo mysql -u root -e "CREATE DATABASE devbank;"
sudo mysql -u root devbank < script.sql

# 3. Iniciar servidor PHP (na pasta do projeto)
cd /caminho/para/Trabalho-Final
php -S localhost:8000

# 4. Aceder: http://localhost:8000
```

### Para testar com dados frescos (reset à base de dados):
```sql
DROP DATABASE IF EXISTS devbank;
CREATE DATABASE devbank;
USE devbank;  -- depois importar script.sql
```

---

## Plano de Testes (Passo a Passo)

### 1. Testar o Admin
| Passo | Ação | Resultado Esperado |
|-------|------|-------------------|
| 1 | Abrir http://localhost/Trabalho-Final/ | Página com 2 opções: Admin e Multibanco |
| 2 | Clicar em **Admin** | Formulário de login |
| 3 | Email: `admin@devbank.com` / Password: `admin123` | Entrar no Dashboard |
| 4 | Clicar **Criar Cliente** | Formulário de registo |
| 5 | Preencher Nome, Email e Password → Criar | Mensagem "Cliente criado com sucesso!" |
| 6 | Clicar **Listar Clientes** | Tabela com o novo cliente + dados de teste |
| 7 | Clicar **Abrir Conta / Emitir Cartão** | Formulário com cliente, tipo de conta e PIN |
| 8 | Selecionar cliente, tipo "Corrente", PIN `1234` → Criar | Mensagem com número do cartão de 16 dígitos |

### 2. Testar o ATM (Multibanco)
| Passo | Ação | Resultado Esperado |
|-------|------|-------------------|
| 1 | Voltar à página inicial → clicar **Multibanco** | Ecrã "Insira o seu cartão" |
| 2 | Cartão: `1234567890123456` / PIN: `cliente123` | Menu principal do multibanco |
| 3 | Clicar **Consultar Saldo** | Saldo atual + últimos movimentos |
| 4 | Voltar → **Levantamento** | Inserir valor → Saldo deduzido |
| 5 | Voltar → **Pagamento de Serviços** | Entidade: `12345`, Referência: `987654321`, Valor: `50` → Saldo deduzido |
| 6 | Voltar → **Transferência** | Conta destino: `1` (ou outra ID), Valor: `100` → Transferido |
| 7 | Clicar **Sair** | Volta ao ecrã inicial do multibanco |

### 3. Testar Conta Poupança (Restrições)
| Passo | Ação | Resultado Esperado |
|-------|------|-------------------|
| 1 | Admin → Abrir Conta → selecionar "Poupança" | Conta criada com novo cartão |
| 2 | ATM → usar novo cartão | Menu disponível |
| 3 | Tentar levantar **€250** | Erro (máximo €200) |
| 4 | Tentar levantar **€190** (com saldo próximo do mínimo) | Erro se saldo - 190 < 20 |

## Funcionalidades POO Implementadas

- **Abstração**: Classe `Utilizador` (abstrata)
- **Herança**: `Admin` e `Cliente` extendem `Utilizador`
- **Polimorfismo**: `ContaCorrente` e `ContaPoupanca` com comportamentos distintos
- **Classe Final**: `ContaPoupanca` (final) com restrições de levantamento
- **Trait**: `HistoricoTrait` reutilizado em `Admin` e `Conta`
- **PDO Prepared Statements**: Todas as queries usam placeholders
- **Transações**: Pagamentos e transferências usam beginTransaction/commit/rollback
