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

$municipalities = $pdo->query('SELECT id, name, state FROM municipalities ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $municipalityId = (int) ($_POST['municipality_id'] ?? 0);
    $inepCode = trim($_POST['inep_code'] ?? '');

    if ($name === '' || $municipalityId === 0) {
        $errors[] = 'Informe o nome e o município.';
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO schools (municipality_id, name, inep_code)
             VALUES (:municipality_id, :name, :inep_code)'
        );
        $stmt->execute([
            'municipality_id' => $municipalityId,
            'name' => $name,
            'inep_code' => $inepCode !== '' ? $inepCode : null,
        ]);
        $success = 'Escola cadastrada com sucesso.';
    }
}

$schoolsStmt = $pdo->query(
    'SELECT schools.id, schools.name, schools.inep_code, municipalities.name AS municipality_name, municipalities.state
     FROM schools
     INNER JOIN municipalities ON municipalities.id = schools.municipality_id
     ORDER BY schools.name'
);
$schools = $schoolsStmt->fetchAll();

require_once __DIR__ . '/../partials/header.php';
?>

<section class="page">
    <div class="page__header">
        <h1>Escolas</h1>
        <p class="muted">Cadastre escolas vinculadas aos municípios.</p>
    </div>

    <div class="grid">
        <div class="card form-card">
            <h3>Nova escola</h3>

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

                <label for="municipality_id">Município</label>
                <select id="municipality_id" name="municipality_id" required>
                    <option value="">Selecione</option>
                    <?php foreach ($municipalities as $municipality) : ?>
                        <option value="<?php echo (int) $municipality['id']; ?>">
                            <?php echo htmlspecialchars($municipality['name'], ENT_QUOTES, 'UTF-8'); ?>
                            (<?php echo htmlspecialchars($municipality['state'], ENT_QUOTES, 'UTF-8'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="inep_code">Código INEP (opcional)</label>
                <input type="text" id="inep_code" name="inep_code">

                <button type="submit" class="button button--primary">Salvar</button>
            </form>
        </div>

        <div class="card">
            <h3>Lista de escolas</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Escola</th>
                        <th>Município</th>
                        <th>INEP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schools as $school) : ?>
                        <tr>
                            <td><?php echo (int) $school['id']; ?></td>
                            <td><?php echo htmlspecialchars($school['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($school['municipality_name'], ENT_QUOTES, 'UTF-8'); ?>
                                (<?php echo htmlspecialchars($school['state'], ENT_QUOTES, 'UTF-8'); ?>)
                            </td>
                            <td><?php echo htmlspecialchars($school['inep_code'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
