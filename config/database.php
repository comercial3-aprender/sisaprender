<?php
declare(strict_types=1);

$dbConfig = [
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'sisaprender',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['database'],
        $dbConfig['charset']
    );
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $exception) {
    die('Falha na conexão com o banco de dados. Verifique as configurações.');
}
