<?php
declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use App\Http\Controllers\AdminProductFilter;
use App\Http\Controllers\ProductFilter;

$isDirect = realpath(__FILE__)  === realpath($_SERVER['SCRIPT_FILENAME'] ?? '');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if ($isDirect && !headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

$products = $_SESSION['products_list'] ?? [];
$sort = $_GET['sort'] ?? 'title';
$view = $_GET['view'] ?? 'cards';

switch ($sort) {
    case 'title':
        ProductFilter::byName($products);
        break;
    case 'price':
        ProductFilter::byPriceAsc($products);
        break;
    case '-price':
        ProductFilter::byPriceDesc($products);
        break;
    case '-stock':
        AdminProductFilter::byRemainingDesc($products);
        break;
    case 'stock':
        AdminProductFilter::byRemainingAsc($products);
        break;
    default:
        ProductFilter::byName($products);
}

if ($view === 'admin') {
    renderAdminRows($products);
} else {
    renderCardGrid($products);
}

if ($isDirect) {exit();} else {return;}

function renderCardGrid(array $products): void {
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
}

function renderAdminRows($products): void {
    foreach ($products as $p):
        $id   = (int)($p['id'] ?? 0);
        $name = htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $price = (string)($p['price'] ?? '0');
        $remaining = (int)($p['remaining'] ?? 0);
        $img  = htmlspecialchars($p['img'] ?? 'Изображение отсутствует', ENT_QUOTES, 'UTF-8');
        ?>
        <tr data-skeleton>
            <td><?= $id ?></td>
            <td><?= $name ?></td>
            <td><?= $price ?></td>
            <td><?= $remaining > 0 ? $remaining : 'Товара не осталось' ?></td>
            <td class="helper"><?= $img ?></td>
            <td>
                <div style="display:flex; gap:8px;">
                    <button class="btn" disabled>Редактировать</button>
                    <button class="btn btn--danger" disabled>Удалить</button>
                </div>
            </td>
        </tr>
    <?php endforeach;
}

