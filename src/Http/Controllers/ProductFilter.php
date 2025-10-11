<?php

namespace App\Http\Controllers;

use Normalizer;

class ProductFilter
{

    /**
     * Метод фильтрует массив по имени от А до Я.
     * Требуется ext-intl для идеальной сортировки, есть фолбэк без него.
     *
     * @param array $products
     * @return void
     */
    public static function byName(array &$products): void //TODO не самое лучшее решение, изменить подход. Сокрытие потенциального фатала.
    {
        $locate = 'ru_RU';
        $useIntl = class_exists('\Collator');
        $collator = $useIntl ? new \Collator($locate) : null;

        $decorated = [];
        foreach ($products as $i => $product) {
            $name = (string)$product['name'] ?? '';
            $decorated[] = [
                'i' => $i,
                'item' => $product,
                'name' => $name,
                'key' => self::normalizeName($name),
            ];
        }

        usort($decorated, function ($a, $b) use ($collator, $useIntl) {
            if ($useIntl) { // TODO убрать проверку
                $cmp = $collator->compare($a['name'], $b['name']);
                if ($cmp !== 0) return $cmp;
            } else {
                $cmp = strcmp($a['key'], $b['key']);
                if ($cmp !== 0) return $cmp;
            }
            $cmp = strcmp($a['key'], $b['key']);
            return ($cmp !== 0) ? $cmp : ($a['i'] <=> $b['i']);
        });

        $sorted = [];
        foreach ($decorated as $row) {
            $sorted[] = $row['item'];
        }
        $products = $sorted;
    }

    /**
     * Метод нормализует строку для работы сортировки
     *
     * @param string $name
     * @return string
     */
    private static function normalizeName(string $name): string
    {
        if (class_exists('Normalizer')) {
            $name = Normalizer::normalize($name, Normalizer::FORM_C);
        }

        $name = trim($name);
        $name = preg_replace('/^[^\p{L}\p{N}]+/u', '', $name) ?? $name;
        $name = mb_strtolower($name, 'UTF-8');
        $name = str_replace(['ё','й'],['е','и'], $name);
        return $name;
    }

    /**
     * Метод фильтрует массив по убыванию цен
     *
     * @param array $products
     * @return void
     */
    public static function byPriceDesc(array &$products): void
    {
        uasort($products, function ($a, $b) {
            return $b['price'] <=> $a['price'];
        });
    }

    /**
     * Метод фильтрует цен по возрастанию цен
     *
     * @param array $products
     * @return void
     */
    public static function byPriceAsc(array &$products): void
    {
        uasort($products, function ($a, $b) {
            return $a['price'] <=> $b['price'];
        });
    }
}


