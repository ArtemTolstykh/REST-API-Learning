<?php
namespace App\Infrastructure\Persistence;

use PDO;

class ProductsRepository { //TODO реализация на абстрактном классе, как ORM. Почитать про принцип работы ORM.
    public function __construct(private PDO $pdo)
    {

    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Products ORDER BY id DESC"); //Todo заменить селекты на класс ProductRepository
        return $this->pdo->query($stmt)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM Products WHERE id = ?");
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function create(string $name, float $price, int $remaining): int
    {
        $st = $this->pdo->prepare("INSERT INTO Products (name, price, remaining) VALUES (?, ?, ?)");
        $st->execute([$name, $price, $remaining]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, float $price, int $remaining): bool
    {
        $st = $this->pdo->prepare("UPDATE Products SET name = ?, price = ?, remaining = ? WHERE id = ?");
        return $st->execute([$name, $price, $remaining, $id]);
    }

    public function delete(int $id): bool
    {
        $st = $this->pdo->prepare("DELETE FROM Products WHERE id = ?");
        return $st->execute([$id]);
    }
}