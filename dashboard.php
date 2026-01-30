<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

require_login();

$user = current_user();

require_once __DIR__ . '/partials/header.php';
?>

<section class="page">
    <div class="page__header">
        <h1>Olá, <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="muted">Seu painel conforme o perfil: <?php echo htmlspecialchars($user['role_name'], ENT_QUOTES, 'UTF-8'); ?>.</p>
    </div>

    <div class="stats-grid">
        <div class="card">
            <h3>Planos em andamento</h3>
            <p class="stat">12</p>
            <span class="muted">Atualize metas e prazos.</span>
        </div>
        <div class="card">
            <h3>Escolas vinculadas</h3>
            <p class="stat">8</p>
            <span class="muted">Acompanhe o progresso.</span>
        </div>
        <div class="card">
            <h3>Relatórios</h3>
            <p class="stat">4</p>
            <span class="muted">Últimos 30 dias.</span>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h3>Dimensões avaliadas</h3>
            <ul class="list">
                <li>Político-Institucional</li>
                <li>Pedagógico</li>
                <li>Administrativo-Financeira</li>
                <li>Pessoal e Relacional</li>
            </ul>
        </div>
        <div class="card">
            <h3>Próximas ações</h3>
            <p class="muted">Aqui serão exibidas as metas com prazo próximo.</p>
            <button class="button button--primary">Criar plano de ação</button>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
