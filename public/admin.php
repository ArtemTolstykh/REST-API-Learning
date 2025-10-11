<?php
require_once __DIR__ . '/header.php';

global $rowsHtml;
$_GET['view'] = 'admin';

?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <title>Маркетплейс — Админка</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="styles/styles.css" rel="stylesheet" />
</head>
<body>
<header class="container header">
    <div class="brand">
        <div class="brand__logo" aria-hidden="true"></div>
        <div class="brand__title">Админка</div>
    </div>
    <nav class="nav">
        <a href="index.php">Витрина</a>
        <a href="admin.php">Админка</a>
    </nav>
</header>

<main class="container" style="display:grid; gap:16px;">
    <!-- Панель добавления -->
    <section class="panel">
        <h3 style="margin-top:0;">Добавить/редактировать товар</h3>
        <form class="form" id="productForm" data-form="product">
            <div class="form__row">
                <div>
                    <label class="label" for="title">Название*</label>
                    <input class="input" id="title" name="title" type="text" placeholder="Напр. 'Швабра PRO Max'" required />
                </div>
                <div>
                    <label class="label" for="price">Цена (₽)*</label>
                    <input class="input" id="price" name="price" type="number" min="0" step="0.01" placeholder="1999.00" required />
                </div>
            </div>
            <div class="form__row">
                <div>
                    <label class="label" for="stock">Остаток*</label>
                    <input class="input" id="stock" name="stock" type="number" min="0" step="1" placeholder="10" required />
                </div>
                <div>
                    <label class="label" for="image_url">Ссылка на изображение</label>
                    <input class="input" id="image_url" name="image_url" type="url" placeholder="https://..." />
                </div>
            </div>

            <!-- Скрытые поля для редактирования -->
            <input type="hidden" id="productId" name="id" value="" data-field="product:id" />

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button class="btn btn--primary" type="button" data-action="product:create">Добавить</button>
                <button class="btn" type="button" data-action="product:update">Сохранить изменения</button>
                <button class="btn btn--ghost" type="reset" data-action="product:reset">Сброс</button>
            </div>
            <div class="helper">Поля с * обязательны. Здесь только вёрстка — подключи REST API в своём JS.</div>
        </form>
    </section>

    <!-- Поиск/сортировка -->
    <section class="panel">
        <div class="toolbar">
            <input class="input" type="search" placeholder="Поиск по названию…" id="adminSearch" data-hook="admin:search" />
            <select class="select" id="sortSelect" data-hook="sort">
                <option value="title">По названию</option>
                <option value="-price">Дороже</option>
                <option value="price">Дешевле</option>
                <option value="-stock">Больше остаток</option>
                <option value="stock">Меньше остаток</option>
            </select>
            <button class="btn" id="refreshBtn" data-action="reload">Обновить</button>
        </div>
        <div class="panel" style="padding:0;">
            <table aria-label="Товары">
                <thead>
                <tr>
                    <th style="width:64px;">ID</th>
                    <th>Название</th>
                    <th style="width:120px;">Цена</th>
                    <th style="width:110px;">Остаток</th>
                    <th>Изображение</th>
                    <th style="width:220px;">Действия</th>
                </tr>
                </thead>
                <tbody id="productsTable" data-list="admin:products">
                    <?= $rowsHtml?>
                </tbody>
            </table>

            <div class="footer" id="adminPagination" data-hook="admin:pagination">
                <!-- ТВОЙ JS вставит пагинацию -->
            </div>
        </div>
    </section>
</main>

<footer class="container footer">© Маркетплейс, админ-панель</footer>
<script src="js/products-ui.js"></script>
</body>
</html>
