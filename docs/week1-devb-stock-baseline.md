# Week 1 – Inventory Stock Baseline

## Current Stock Fields

- **Table:** `items`
- **Fields related to stock:** (list any existing columns like `quantity`, `stock`, etc. If none, write "No stock field exists yet.")

## Current Source of Truth

- (Describe where stock data is currently stored. For example: "Currently, there is no stock tracking. All item data is stored only in the `items` table with no quantity field.")

## Risks

- Adding sales deduction later without proper tracking could cause inconsistencies.
- No audit trail for stock changes.
- Negative stock could occur if deduction is applied without validation.

## Week 2 Recommendation

- Introduce a dedicated `inventory_movements` table to record all stock changes.
- Deduct stock only on sale finalization.
- Implement a service to update stock based on movements.
- Add validation to prevent negative stock (configurable).
