# Week 2 – Inventory Deduction Rules (Proposed)

## When to Deduct Stock

- Stock should be deducted **only when a sale is FINALIZED**.
- No deduction on DRAFT or POSTED sales.

## How to Deduct

- Each finalized sale line should create one movement record in a new `inventory_movements` table.
- Movements should reference the sale line ID.
- Quantity should be negative (outgoing).

## Void / Reversal Strategy

- If a sale is voided after finalization, a compensating positive movement should be created.
- Voided sales must be audited.

## Insufficient Stock Handling

- To be decided later (allow negative stock or block sale?). Decision deferred to Week 2.

## Audit Requirements

- Every stock movement must be recorded with `created_at`, `created_by`, and reason.
