<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user && $user['role_slug'] === 'administrador';
}

function require_login(): void
{
    if (!current_user()) {
        header('Location: /index.php');
        exit;
    }
}

function attempt_login(PDO $pdo, string $email, string $password): array
{
    $stmt = $pdo->prepare(
        'SELECT users.id, users.name, users.email, roles.name AS role_name, roles.slug AS role_slug
         FROM users
         INNER JOIN roles ON roles.id = users.role_id
         WHERE users.email = :email
         LIMIT 1'
    );
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Usuário não encontrado.'];
    }

    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = :id');
    $stmt->execute(['id' => $user['id']]);
    $hash = $stmt->fetchColumn();

    if (!$hash || !password_verify($password, $hash)) {
        return ['success' => false, 'message' => 'Senha inválida.'];
    }

    $_SESSION['user'] = $user;
    return ['success' => true];
}
