<?php
namespace App\Infrastructure\Database;

use PDO;

class Db {
    public static function pdo(): PDO
    {
        $host = getenv('DB_HOST');
        $db = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $dns = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        $pdo = new PDO($dns, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    }
}