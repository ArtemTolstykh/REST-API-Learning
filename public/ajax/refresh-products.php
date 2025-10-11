<?php

require __DIR__ . '/../../vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: text/html; charset=UTF-8');

$sort = $_GET['sort'] ?? 'title';

$arResult = \App\Http\Controllers\ProductsController::fetchAll();

$_SESSION['products_list'] = $arResult;

$_GET['sort'] = $sort;

require __DIR__ . '/sort-products.php';