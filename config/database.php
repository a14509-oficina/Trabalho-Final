<?php
// ============================================
// CONFIGURAÇÃO DA BASE DE DADOS
// ============================================
// Deteccao automatica de ambiente
// Se estiver no InfinityFree, cria um ficheiro vazio chamado .prod
// ou define a constante DB_ENV como 'prod'

$isProd = file_exists(__DIR__ . '/../.prod');

if ($isProd) {
    // ========== PRODUÇÃO (InfinityFree) ==========
    define('DB_HOST', 'sqlXXX.infinityfree.com');
    define('DB_NAME', 'if0_XXXXX_devbank');
    define('DB_USER', 'if0_XXXXX');
    define('DB_PASS', 'tua_password_aqui');
} else {
    // ========== LOCAL (XAMPP) ==========
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'devbank');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}
