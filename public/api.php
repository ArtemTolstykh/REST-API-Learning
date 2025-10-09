<?php
declare(strict_types=1);

// === базовая настройка ===
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); exit;
}

// --- утилиты ---
function env(string $key, ?string $default=null): ?string {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

function json_input(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : [];
}

function respond(int $code, $payload): void {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function require_api_key_for_write(): void {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (in_array($method, ['POST','PUT','DELETE'], true)) {
        $key = $_SERVER['HTTP_X_API_KEY'] ?? '';
        if (!$key || $key !== env('API_KEY', '')) {
            respond(401, ['error' => 'Unauthorized']);
        }
    }
}

// --- PDO подключение ---
try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        env('DB_HOST', 'mysql'),
        env('DB_NAME', 'rest_test')
    );
    $pdo = new PDO($dsn, env('DB_USER','user'), env('DB_PASS','pass'), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    respond(500, ['error' => 'DB connection failed', 'details' => $e->getMessage()]);
}

// --- простой роутинг ---
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// health
if ($path === '/api/health' && $method === 'GET') {
    respond(200, ['status' => 'ok']);
}

// /api/products и /api/products/{id}
if (str_starts_with($path, '/api/products')) {
    $parts = array_values(array_filter(explode('/', $path))); // ['api','products','{id?}']

    // Список
    if ($method === 'GET' && count($parts) === 2) {
        $sql = "SELECT id, name, price, remaining
                FROM Products ORDER BY id ASC";
        $rows = $pdo->query($sql)->fetchAll();
        respond(200, $rows);
    }

    // Получить по id
    if ($method === 'GET' && count($parts) === 3) {
        $id = (int)$parts[2];
        $st = $pdo->prepare("SELECT id, name, price, remaining
                             FROM Products WHERE id = ?");
        $st->execute([$id]);
        $row = $st->fetch();
        if (!$row) respond(404, ['error' => 'Not found']);
        respond(200, $row);
    }

    // Создать
    if ($method === 'POST' && count($parts) === 2) {
        require_api_key_for_write();
        $data = json_input();
        $name  = trim((string)($data['name'] ?? ''));
        $price = (float)($data['price'] ?? -1);
        $stock = (int)($data['stock'] ?? -1);
        if ($name === '' || $price < 0 || $stock < 0) {
            respond(422, ['error' => 'Invalid data']);
        }
        $st = $pdo->prepare("INSERT INTO Products (name, price, remaining) VALUES (?, ?, ?)");
        $st->execute([$name, $price, $stock]);
        respond(201, ['id' => (int)$pdo->lastInsertId()]);
    }

    // Обновить
    if ($method === 'PUT' && count($parts) === 3) {
        require_api_key_for_write();
        $id = (int)$parts[2];
        $data = json_input();
        $name  = trim((string)($data['name'] ?? ''));
        $price = (float)($data['price'] ?? -1);
        $stock = (int)($data['stock'] ?? -1);
        if ($name === '' || $price < 0 || $stock < 0) {
            respond(422, ['error' => 'Invalid data']);
        }
        $exists = $pdo->prepare("SELECT id FROM Products WHERE id=?");
        $exists->execute([$id]);
        if (!$exists->fetchColumn()) respond(404, ['error' => 'Not found']);

        $st = $pdo->prepare("UPDATE Products SET name=?, price=?, remaining=? WHERE id=?");
        $st->execute([$name, $price, $stock, $id]);
        respond(200, ['ok' => true]);
    }

    // Удалить
    if ($method === 'DELETE' && count($parts) === 3) {
        require_api_key_for_write();
        $id = (int)$parts[2];
        $exists = $pdo->prepare("SELECT id FROM Products WHERE id=?");
        $exists->execute([$id]);
        if (!$exists->fetchColumn()) respond(404, ['error' => 'Not found']);

        $st = $pdo->prepare("DELETE FROM Products WHERE id=?");
        $st->execute([$id]);
        respond(200, ['ok' => true]);
    }
}

// если не совпало ни с одним роутом
respond(404, ['error' => 'Unknown route']);
