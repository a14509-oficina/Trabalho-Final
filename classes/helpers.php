<?php
require_once __DIR__ . '/Database.php';

// ========== CSRF ==========
function gerarTokenCSRF(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarTokenCSRF(string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function campoCSRF(): string
{
    return '<input type="hidden" name="csrf_token" value="' . gerarTokenCSRF() . '">';
}

// ========== RATE LIMITING (ATM) ==========
function verificarRateLimit(string $key, int $maxTentativas = 3, int $periodoSegundos = 300): bool
{
    $agora = time();
    $registos = $_SESSION[$key] ?? [];

    $registos = array_filter($registos, fn($t) => $t > $agora - $periodoSegundos);

    if (count($registos) >= $maxTentativas) {
        return false;
    }

    return true;
}

function registrarTentativa(string $key): void
{
    $_SESSION[$key][] = time();
}

function tempoRestanteBloqueio(string $key, int $periodoSegundos = 300): int
{
    $registos = $_SESSION[$key] ?? [];
    if (empty($registos)) return 0;
    $agora = time();
    $maisRecente = max($registos);
    $restante = $periodoSegundos - ($agora - $maisRecente);
    return max(0, $restante);
}

// ========== LOG DE ACESSO ==========
function registarLogAcesso(string $tipo, string $identificador, bool $sucesso, string $ip = ''): void
{
    $db = Database::getConnection();
    $ip = $ip ?: ($_SERVER['REMOTE_ADDR'] ?? '');
    $stmt = $db->prepare(
        'INSERT INTO login_log (tipo, identificador, sucesso, ip, created_at) VALUES (:tipo, :identificador, :sucesso, :ip, NOW())'
    );
    $stmt->execute([
        ':tipo' => $tipo,
        ':identificador' => $identificador,
        ':sucesso' => $sucesso ? 1 : 0,
        ':ip' => $ip,
    ]);
}

// ========== GERAR COMPROVATIVO ==========
function gerarComprovativo(string $operacao, float $valor, string $descricao, float $saldoAtual): array
{
    return [
        'data' => date('d/m/Y H:i:s'),
        'operacao' => $operacao,
        'valor' => $valor,
        'descricao' => $descricao,
        'saldo_atual' => $saldoAtual,
        'codigo' => strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)),
    ];
}
