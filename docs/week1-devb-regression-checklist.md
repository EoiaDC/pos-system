# Week 1 – Inventory Regression Checklist

**Date:** **\*\***\_**\*\***  
**Tester:** DEV B

## Preconditions

- [ ] Latest main branch pulled
- [ ] Database migrated (`php migrate.php`)
- [ ] At least one item exists in the database

## Test Steps

| Step | Action                                               | Expected Result                                                              | Actual Result | Pass/Fail |
| ---- | ---------------------------------------------------- | ---------------------------------------------------------------------------- | ------------- | --------- |
| 1    | Post a sale (use the sales UI or test script)        | Sale status becomes POSTED                                                   |               |           |
| 2    | Verify item stock before any payment                 | `ItemStockSummaryService::getCurrentStock($itemId)` returns 0 (or unchanged) |               |           |
| 3    | Record a payment against the sale                    | Payment succeeds                                                             |               |           |
| 4    | Verify item stock still unchanged                    | Stock still 0                                                                |               |           |
| 5    | Finalize the sale (DEV A's finalize action)          | Sale becomes FINALIZED                                                       |               |           |
| 6    | Verify item stock still unchanged                    | Stock still 0                                                                |               |           |
| 7    | Check `items` table – no stock columns were modified | No unexpected changes                                                        |               |           |
| 8    | Check that no inventory-related fields were altered  | All inventory data intact                                                    |               |           |

## Notes

- Any unexpected stock change is a **critical failure**.
- If any step fails, immediately alert the team and do **not** proceed.
