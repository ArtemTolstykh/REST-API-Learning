<?php
declare(strict_types=1);

require __DIR__ . '/../controllers/controllerAllProducts.php';

use App\Controllers\ProductsController;

$arResult = ProductsController::fetchAll();

//var_dump($arResult);
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <title>Маркетплейс — Витрина</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="styles.css" rel="stylesheet" />
</head>
<body>
<header class="container header">
    <div class="brand">
        <div class="brand__logo" aria-hidden="true"></div>
        <div class="brand__title">Маркетплейс</div>
    </div>
    <nav class="nav">
        <a href="index.php">Витрина</a>
        <a href="admin.php">Админка</a>
    </nav>
</header>

<main class="container layout">
    <!-- Витрина -->
    <section aria-label="Витрина товаров" class="panel">
        <div class="toolbar">
            <input class="input" type="search" placeholder="Поиск по названию…" id="searchInput"
                   data-hook="search" />
            <select class="select" id="sortSelect" data-hook="sort">
                <option value="-created_at">Сначала новые</option>
                <option value="title">По названию (А→Я)</option>
                <option value="-price">Дороже</option>
                <option value="price">Дешевле</option>
            </select>
            <button class="btn" id="refreshBtn" data-action="reload">Обновить</button>
        </div>

        <div class="grid" id="productsGrid" data-list="products">
            <!-- СЮДА РЕНДЕРИТСЯ СПИСОК ТОВАРОВ ЧЕРЕЗ API -->
            <!-- Пример карточки для визуала (будет удалена твоим JS): -->
            <?php foreach ($arResult as $arItems): ?>
            <article class="card" data-skeleton>
                <div class="card__body">
                    <div class="card__title"><?php echo $arItems['name'] ?></div>
                    <div class="card__meta">
                        <span class="badge">₽ <?php echo $arItems['price']?></span>
                        <?php if($arItems['remaining'] > 0):?>
                            <span class="badge badge--ok">В наличии, <?php echo $arItems['remaining']?> шт.</span>
                        <?php else: ?>
                            <span class="badge badge--no">Нет в наличии</span>
                        <?php endif; ?>
                    </div>
                    <div class="card__actions">
                        <button class="btn btn--primary" disabled>В корзину</button>
                        <button class="btn btn--ghost" disabled>Подробнее</button>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="footer">
        <span id="pagination" data-hook="pagination">
          <!-- ТВОЙ JS может вставить сюда пагинацию → «← Назад | Стр. 1 из 5 | Вперёд →» -->
        </span>
        </div>
    </section>

    <!-- Корзина -->
    <aside class="cart panel" aria-label="Корзина">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0;">Корзина</h3>
            <button class="btn btn--danger" id="clearCartBtn" data-action="cart:clear">Очистить</button>
        </div>
        <div class="cart__list" id="cartList" data-list="cart">
            <!-- Элементы корзины рендерятся твоим JS -->
            <div class="helper" data-empty-hint>Корзина пуста</div>
        </div>
        <hr class="sep" />
        <div class="cart__footer">
            <div>
                <div class="helper">Итого</div>
                <div class="cart__price" id="cartTotal" data-field="cart:total">₽ 0</div>
            </div>
            <button class="btn btn--success" id="checkoutBtn" data-action="cart:checkout">Оформить</button>
        </div>
    </aside>
</main>

<footer class="container footer">© Маркетплейс, учебный проект</footer>
</body>
</html>