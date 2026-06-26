<?php
$file = __DIR__ . '/.htaccess';
$normal = "Options -Indexes\n";
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'aplicar') {
        file_put_contents($file, $normal);
        $msg = '.htaccess aplicado com Options -Indexes';
    } elseif ($action === 'remover') {
        if (file_exists($file)) unlink($file);
        $msg = '.htaccess removido';
    }
    $current = file_exists($file) ? file_get_contents($file) : '(sem ficheiro)';
} else {
    $current = file_exists($file) ? file_get_contents($file) : '(sem ficheiro)';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>.htaccess</title>
    <style>
        body{font-family:monospace;padding:40px;background:#1a2a4a;color:#fff}
        pre{background:rgba(0,0,0,0.3);padding:15px;border-radius:6px}
        button{padding:12px 24px;border:none;border-radius:6px;cursor:pointer;font-size:14px;margin:5px}
        .btn-g{background:#2ecc71;color:#fff}
        .btn-r{background:#e74c3c;color:#fff}
        .msg{padding:10px;border-radius:6px;margin:10px 0;background:rgba(46,204,113,0.2)}
    </style>
</head>
<body>
    <h1>.htaccess</h1>
    <pre><?= htmlspecialchars($current) ?></pre>
    <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
    <form method="POST">
        <button type="submit" name="action" value="aplicar" class="btn-g">Aplicar (Options -Indexes)</button>
        <button type="submit" name="action" value="remover" class="btn-r">Remover .htaccess</button>
    </form>
    <p style="margin-top:20px;color:#889;font-size:12px;">Remove este ficheiro depois de usar.</p>
</body>
</html>
