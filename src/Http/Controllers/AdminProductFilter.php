<?php

namespace App\Http\Controllers;

class AdminProductFilter extends ProductFilter
{

    /**
     * Фильтрует остаток по возрастанию
     *
     * @param array $products
     * @return void
     */
    public static function byRemainingAsc(array &$products): void
    {
        usort($products, function ($a, $b) {
            return $a['remaining'] <=> $b['remaining'];
        });
    }

    /**
     * Фильтрует остаток по убыванию
     *
     * @param array $products
     * @return void
     */
    public static function byRemainingDesc(array &$products): void
    {
        usort($products, function ($a, $b) {
            return $b['remaining'] <=> $a['remaining'];
        });
    }
}