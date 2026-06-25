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
    define('DB_HOST', 'sql300.infinityfree.com');
    define('DB_NAME', 'if0_42203983_devbank');
    define('DB_USER', 'if0_42203983');
    define('DB_PASS', 'kd7texfgXcf9n');
} else {
    // ========== LOCAL ==========
    // Se der "No such file or directory" usa '127.0.0.1'
    // Se der "Connection refused" usa 'localhost'
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'devbank');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}
