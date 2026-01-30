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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $state = strtoupper(trim($_POST['state'] ?? ''));

        if ($name === '' || $state === '') {
            $errors[] = 'Informe o nome e a UF.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO municipalities (name, state) VALUES (:name, :state)');
            $stmt->execute(['name' => $name, 'state' => $state]);
            $success = 'Município cadastrado com sucesso.';
        }
    }

    if ($action === 'import_ibge') {
        $ibgeUrl = 'https://servicodados.ibge.gov.br/api/v1/localidades/municipios';
        $response = file_get_contents($ibgeUrl);

        if ($response === false) {
            $errors[] = 'Não foi possível acessar a API do IBGE.';
        } else {
            $data = json_decode($response, true);
            if (!is_array($data)) {
                $errors[] = 'Resposta inválida da API do IBGE.';
            } else {
                $inserted = 0;
                $existsStmt = $pdo->prepare('SELECT id FROM municipalities WHERE name = :name AND state = :state LIMIT 1');
                $insertStmt = $pdo->prepare('INSERT INTO municipalities (name, state) VALUES (:name, :state)');

                foreach ($data as $municipio) {
                    $name = $municipio['nome'] ?? '';
                    $state = $municipio['microrregiao']['mesorregiao']['UF']['sigla'] ?? '';

                    if ($name === '' || $state === '') {
                        continue;
                    }

                    $existsStmt->execute(['name' => $name, 'state' => $state]);
                    if (!$existsStmt->fetchColumn()) {
                        $insertStmt->execute(['name' => $name, 'state' => $state]);
                        $inserted++;
                    }
                }

                $success = "Importação concluída. Municípios inseridos: {$inserted}.";
            }
        }
    }
}

$municipalities = $pdo->query('SELECT id, name, state FROM municipalities ORDER BY name')->fetchAll();

require_once __DIR__ . '/../partials/header.php';
?>

<section class="page">
    <div class="page__header">
        <h1>Municípios</h1>
        <p class="muted">Cadastre e importe municípios via API do IBGE.</p>
    </div>

    <div class="grid">
        <div class="card form-card">
            <h3>Novo município</h3>

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
                <input type="hidden" name="action" value="create">
                <label for="name">Nome</label>
                <input type="text" id="name" name="name" required>

                <label for="state">UF</label>
                <input type="text" id="state" name="state" maxlength="2" required>

                <button type="submit" class="button button--primary">Salvar</button>
            </form>

            <form method="post" class="form">
                <input type="hidden" name="action" value="import_ibge">
                <button type="submit" class="button button--secondary">Importar municípios (IBGE)</button>
            </form>
        </div>

        <div class="card">
            <h3>Lista de municípios</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Município</th>
                        <th>UF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($municipalities as $municipality) : ?>
                        <tr>
                            <td><?php echo (int) $municipality['id']; ?></td>
                            <td><?php echo htmlspecialchars($municipality['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($municipality['state'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
