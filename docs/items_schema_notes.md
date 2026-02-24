# Items Table Schema Notes

## Price Column Information

**Date:** 2026-02-24
**Module:** Inventory (DEV B)

### Current Schema

The `items` table contains the following price-related column:

| Column  | Type          | Default | Description            |
| ------- | ------------- | ------- | ---------------------- |
| `price` | DECIMAL(12,2) | 0.00    | Standard selling price |

### Verification

- ✅ Price column exists: `price` DECIMAL(12,2) NOT NULL DEFAULT 0.00
- ❌ No separate `selling_price` or `unit_price` columns

### Recommendation for Sales Module

The `price` column is available for use in the sales module.
This column should be used as the standard selling price for items.

### Sample Data

Sample items have been added with prices:

- Item ID 1: Sample Item (price: 19.99)
- Item ID 2: Sample Item (price: 19.99)
- Item ID 3: Sample Item (price: 19.99)

### Notes

- All sample items use the same price (19.99) for testing
- Prices can be updated per item as needed
- The column supports up to 12 digits with 2 decimal places
