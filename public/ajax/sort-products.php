<?php
declare(strict_types=1);

use App\Controllers\ProductFilter;

require __DIR__ . '/../../controllers/ProductFilter.php';

session_start();

header('Content-Type: text/html; charset=UTF-8');

$sort = $_GET['sort'] ?? 'title';
$products = $_SESSION['products_list'] ?? [];

if (!$products) {exit;}

switch ($sort) {
    case 'title':
        ProductFilter::byName($products);
        break;
    case 'price':
        ProductFilter::byPriceExpensive($products);
        break;
    case '-price':
        ProductFilter::byPriceCheaper($products);
        break;
    default:
        ProductFilter::byName($products);
}

foreach ($products as $p):
    $name = htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $price = number_format((float)($p['price'] ?? 0), 2, ',', ' ');
    $remaining = (int)($p['remaining'] ?? 0);
    ?>
    <article class="card" data-id="<?= (int)($p['id'] ?? 0) ?>">
        <div class="card__body">
            <div class="card__title"><?= $name ?></div>
            <div class="card__meta">
                <span class="badge">₽ <?= $price ?></span><br>
                <?php if ($remaining > 0): ?>
                    <span class="badge badge--ok">В наличии, <?= $remaining ?> шт.</span>
                <?php else: ?>
                    <span class="badge badge--no">Нет в наличии</span>
                <?php endif; ?>
            </div>
            <div class="card__actions">
                <button class="btn btn--primary">В корзину</button>
                <button class="btn btn--ghost" disabled>Подробнее</button>
            </div>
        </div>
    </article>
<?php endforeach;