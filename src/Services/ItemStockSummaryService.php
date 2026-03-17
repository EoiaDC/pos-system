<?php

namespace POS\Services;

use POS\Database\DB;

class ItemStockSummaryService
{
    public function getCurrentStock(int $itemId): array
    {
        $item = DB::fetch(
            "SELECT id, sku, name FROM items WHERE id = ?",
            [$itemId]
        );

        if (!$item) {
            return [
                'item_id'     => $itemId,
                'sku'         => '',
                'item_name'   => '',
                'stock_on_hand' => 0.00,
            ];
        }

        // No real stock field yet – return 0
        return [
            'item_id'       => (int)$item['id'],
            'sku'           => $item['sku'],
            'item_name'     => $item['name'],
            'stock_on_hand' => 0.00,
        ];
    }
}
