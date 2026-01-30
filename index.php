<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

$errors = [];
if (current_user()) {
    header('Location: /dashboard.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errors[] = 'Informe e-mail e senha para continuar.';
    } else {
        $result = attempt_login($pdo, $email, $password);
        if (!empty($result['success'])) {
            header('Location: /dashboard.php');
            exit;
        }

        $errors[] = $result['message'] ?? 'Não foi possível autenticar.';
    }
}

require_once __DIR__ . '/partials/header.php';
?>

<section class="login">
    <div class="login__intro">
        <h1>Gestão do Plano de Ação Escolar</h1>
        <p>
            Plataforma para diretores escolares elaborarem planos de ação nas dimensões
            Político-Institucional, Pedagógico, Administrativo-Financeira e Pessoal e Relacional.
        </p>
        <div class="login__cards">
            <div class="card">
                <h3>Perfis de acesso</h3>
                <ul>
                    <li>Administrador</li>
                    <li>Coordenador</li>
                    <li>Formador</li>
                    <li>Secretaria de Educação</li>
                    <li>Gestor Escolar</li>
                </ul>
            </div>
            <div class="card card--highlight">
                <h3>Dashboard inteligente</h3>
                <p>Indicadores, gráficos e acompanhamento por escola e município.</p>
            </div>
        </div>
    </div>

    <div class="login__panel">
        <h2>Entrar</h2>
        <p class="muted">Acesse com seu e-mail institucional.</p>
        <div class="login__helper">
            <p><strong>Acesso inicial:</strong> admin@sisaprender.local</p>
            <p><strong>Senha:</strong> admin123</p>
        </div>

        <?php if ($errors) : ?>
            <div class="alert">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="nome@escola.edu.br" required>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>

            <button type="submit" class="button button--primary">Acessar</button>
            <a href="/reset-password.php" class="link">Esqueci minha senha</a>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
