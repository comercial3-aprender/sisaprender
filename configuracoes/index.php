<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';

require_login();
if (!is_admin()) {
    header('Location: /dashboard.php');
    exit;
}

require_once __DIR__ . '/../partials/header.php';
?>

<section class="page">
    <div class="page__header">
        <h1>Configurações</h1>
        <p class="muted">Área destinada ao administrador para cadastro das informações básicas.</p>
    </div>

    <div class="card">
        <h3>Em construção</h3>
        <p>Em breve será possível cadastrar municípios, escolas, usuários e parâmetros do sistema.</p>
        <a href="/configuracoes/usuarios.php" class="button button--primary">Cadastrar usuário</a>
        <a href="/configuracoes/municipios.php" class="button button--secondary">Municípios</a>
        <a href="/configuracoes/escolas.php" class="button button--secondary">Escolas</a>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
