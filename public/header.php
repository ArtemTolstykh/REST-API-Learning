<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Http\Controllers\ProductsController;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$arResult = ProductsController::fetchAll(); //TODO использовать не массив а модель, modelViewController паттерн проектирования
$_SESSION['products_list'] = $arResult;

$initialSort = $_GET['sort'] ?? 'title';

ob_start();
$_GET['sort'] = $initialSort;

require __DIR__ . '/ajax/sort-products.php';
$cardsHtml = ob_get_clean();