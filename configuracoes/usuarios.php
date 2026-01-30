<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

require_login();
if (!is_admin()) {
    header('Location: /dashboard.php');
    exit;
}

$errors = [];
$success = '';

$rolesStmt = $pdo->query('SELECT id, name FROM roles ORDER BY name');
$roles = $rolesStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $roleId = (int) ($_POST['role_id'] ?? 0);
    $municipalityId = $_POST['municipality_id'] !== '' ? (int) $_POST['municipality_id'] : null;
    $schoolId = $_POST['school_id'] !== '' ? (int) $_POST['school_id'] : null;

    if ($name === '' || $email === '' || $password === '' || $roleId === 0) {
        $errors[] = 'Preencha nome, e-mail, senha e perfil.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (role_id, municipality_id, school_id, name, email, password_hash)
                 VALUES (:role_id, :municipality_id, :school_id, :name, :email, :password_hash)'
            );
            $stmt->execute([
                'role_id' => $roleId,
                'municipality_id' => $municipalityId,
                'school_id' => $schoolId,
                'name' => $name,
                'email' => $email,
                'password_hash' => $hash,
            ]);
            $success = 'Usuário criado com sucesso.';
        } catch (PDOException $exception) {
            $errors[] = 'Não foi possível criar o usuário. Verifique o e-mail.';
        }
    }
}

require_once __DIR__ . '/../partials/header.php';
?>

<section class="page">
    <div class="page__header">
        <h1>Cadastrar usuário</h1>
        <p class="muted">Crie acessos para os perfis do sistema.</p>
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
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>

            <label for="role_id">Perfil</label>
            <select id="role_id" name="role_id" required>
                <option value="">Selecione</option>
                <?php foreach ($roles as $role) : ?>
                    <option value="<?php echo (int) $role['id']; ?>">
                        <?php echo htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="municipality_id">Município (opcional)</label>
            <input type="number" id="municipality_id" name="municipality_id" min="1" placeholder="ID do município">

            <label for="school_id">Escola (opcional)</label>
            <input type="number" id="school_id" name="school_id" min="1" placeholder="ID da escola">

            <button type="submit" class="button button--primary">Salvar usuário</button>
            <a href="/configuracoes" class="link">Voltar</a>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
