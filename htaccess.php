<?php
$file = __DIR__ . '/.htaccess';

// Safe .htaccess that works on InfinityFree (LiteSpeed)
$safe = "Options -Indexes\n";

// Full security .htaccess (may cause 500 on some LiteSpeed)
$full = "Options -Indexes
ServerSignature Off

<FilesMatch \"\\.(sql|md)$\">
  Deny from all
</FilesMatch>

<FilesMatch \"^\\\.\">
  Deny from all
</FilesMatch>

<IfModule rewrite_module>
  RewriteEngine On
  RewriteRule ^(config|classes|setup)/ - [F,L]
  RewriteRule ^script\\.sql$ - [F,L]
</IfModule>
";

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? '';
    if ($mode === 'safe') {
        file_put_contents($file, $safe);
        $msg = '✅ .htaccess atualizado para modo seguro (Options -Indexes)';
    } elseif ($mode === 'full') {
        file_put_contents($file, $full);
        $msg = '✅ .htaccess atualizado para modo completo (com segurança)';
    } elseif ($mode === 'remove') {
        if (file_exists($file)) unlink($file);
        $msg = '✅ .htaccess removido';
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
    <title>Gestor .htaccess</title>
    <style>
        body { font-family: monospace; padding: 40px; background: #1a2a4a; color: #fff; }
        .box { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin: 10px 0; }
        pre { background: rgba(0,0,0,0.3); padding: 15px; border-radius: 6px; overflow-x: auto; font-size: 12px; }
        button { padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; margin: 5px; }
        .btn-safe { background: #2ecc71; color: #fff; }
        .btn-full { background: #f39c12; color: #fff; }
        .btn-remove { background: #e74c3c; color: #fff; }
        .msg { padding: 10px; border-radius: 6px; margin: 10px 0; background: rgba(46,204,113,0.2); border: 1px solid rgba(46,204,113,0.3); }
        h1 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Gestor .htaccess</h1>
    <div class="box">
        <p><strong>Conteúdo atual:</strong></p>
        <pre><?= htmlspecialchars($current) ?></pre>
    </div>
    <?php if ($msg): ?>
        <div class="msg"><?= $msg ?></div>
    <?php endif; ?>
    <form method="POST">
        <button type="submit" name="mode" value="safe" class="btn-safe" onclick="return confirm('Aplicar .htaccess seguro?')">Seguro (só Options)</button>
        <button type="submit" name="mode" value="full" class="btn-full" onclick="return confirm('Aplicar .htaccess completo?')">Completo (com segurança)</button>
        <button type="submit" name="mode" value="remove" class="btn-remove" onclick="return confirm('Remover .htaccess?')">Remover</button>
    </form>
    <p style="margin-top:20px;color:#889;font-size:12px;">Depois de alterar, <strong>apaga este ficheiro</strong> (htaccess.php) do servidor.</p>
</body>
</html>
