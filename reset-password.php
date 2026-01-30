<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if ($email === '' || $password === '' || $confirm === '') {
        $errors[] = 'Preencha todos os campos.';
    } elseif ($password !== $confirm) {
        $errors[] = 'As senhas não conferem.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $userId = $stmt->fetchColumn();

        if (!$userId) {
            $errors[] = 'Usuário não encontrado.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $update = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
            $update->execute(['hash' => $hash, 'id' => $userId]);
            $success = 'Senha atualizada com sucesso. Faça login novamente.';
        }
    }
}

require_once __DIR__ . '/partials/header.php';
?>

<section class="page">
    <div class="page__header">
        <h1>Reset de senha</h1>
        <p class="muted">Informe seu e-mail e defina uma nova senha.</p>
    </div>

    <div class="card form-card">
        <?php if ($errors) : ?>
            <div class="alert">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success) : ?>
            <div class="alert alert--success">
                <p><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endif; ?>

        <form method="post" class="form">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Nova senha</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirmar senha</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" class="button button--primary">Atualizar senha</button>
            <a href="/index.php" class="link">Voltar ao login</a>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
