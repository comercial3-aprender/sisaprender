<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$assetBase = str_starts_with($_SERVER['REQUEST_URI'], '/configuracoes') ? '../' : '';
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisAprender | Plano de Ação</title>
    <link rel="stylesheet" href="<?php echo $assetBase; ?>assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="topbar__brand">
            <span class="brand__logo">SA</span>
            <div class="brand__text">
                <strong>SisAprender</strong>
                <small>Plano de Ação Escolar</small>
            </div>
        </div>
        <nav class="topbar__nav">
            <?php if ($user) : ?>
                <a href="/dashboard.php" class="topbar__link">Dashboard</a>
                <?php if (($user['role_slug'] ?? '') === 'administrador') : ?>
                    <a href="/configuracoes" class="topbar__link">Configurações</a>
                <?php endif; ?>
                <a href="/logout.php" class="topbar__link">Sair</a>
            <?php else : ?>
                <a href="/index.php" class="topbar__link">Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="main">
