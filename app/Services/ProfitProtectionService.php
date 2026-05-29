<?php

namespace App\Services;

class ProfitProtectionService
{
    public static function validateItemPrice($product, float $sellingPrice): ?string
    {
        if (!$product) return null;

        $buyingPrice = (float) ($product->buying_price ?? 0);

        if ($buyingPrice > 0 && $sellingPrice < $buyingPrice) {
            return "Selling price (\${$sellingPrice}) is below buying price (\${$buyingPrice}) for {$product->name}.";
        }

        return null;
    }
}
