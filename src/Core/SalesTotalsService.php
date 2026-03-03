<?php
namespace App\Core;

class SalesTotalsService
{
    /**
     * Recompute subtotal and grand total for a given sale, and update the header.
     * @param int $saleId
     * @throws \Exception
     */
    public static function recompute(int $saleId): void
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            // Load all lines for this sale – now including line_discount
            $stmt = $pdo->prepare("
                SELECT qty, unit_price, line_discount 
                FROM sales_lines 
                WHERE sale_id = ?
            ");
            $stmt->execute([$saleId]);
            $lines = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $grossSubtotal = 0.0;
            $totalDiscount = 0.0;

            foreach ($lines as $line) {
                $grossSubtotal += (float)$line['qty'] * (float)$line['unit_price'];
                $totalDiscount += (float)$line['line_discount'];
            }

            // Grand total cannot be negative – clamp to 0
            $grandTotal = max(0, $grossSubtotal - $totalDiscount);

            // Update header
            $updateStmt = $pdo->prepare("
                UPDATE sales_headers
                SET subtotal = :subtotal,
                    discount_total = :discount_total,
                    grand_total = :grand_total,
                    vatable_sales = 0,
                    vat_amount = 0,
                    vat_exempt_sales = 0,
                    zero_rated_sales = 0,
                    updated_at = NOW()
                WHERE id = :sale_id
            ");
            $updateStmt->execute([
                ':subtotal' => $grossSubtotal,
                ':discount_total' => $totalDiscount,
                ':grand_total' => $grandTotal,
                ':sale_id' => $saleId
            ]);

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}