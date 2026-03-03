# Audit Events for Draft Sale Setup

## sale.register.selected
- **When**: User selects a POS register for the current draft sale.
- **Where**: `SaleSetupController::setRegister()`
- **Metadata**:
  - `sale_id` (int)
  - `pos_register_id` (int)
  - `actor_user_id` (should be automatically set by Auditor if not provided)
- **Example usage**:
  ```php
  $event = new AuditEvent('sale.register.selected', 'sales_header');
  $event->actor_user_id = Auth::userId();
  $event->entity_id = $saleId;
  $event->meta = ['pos_register_id' => $registerId];
  Auditor::record($event);


  ## sale.started
- **When**: A new draft sale is created (via `SalesDraftService::createDraft`).
- **Metadata**:
  - `sale_id` (int)
  - `status` (string) – always "DRAFT"
  - `note` (string) – optional description
- **Example**:
  ```php
  Auditor::log('sale.started', [
      'entity_id' => $saleId,
      'meta' => ['status' => 'DRAFT']
  ]);


  ## sale.line.added
- **When**: A line item is added to a draft sale.
- **Metadata**:
  - `sale_id` (int)
  - `line_id` (int)
  - `item_id` (int)
  - `qty` (float)
  - `unit_price` (float)
- **Example**:
  ```php
  Auditor::log('sale.line.added', [
      'entity_id' => $saleId,
      'meta' => [
          'line_id' => $lineId,
          'item_id' => $itemId,
          'qty' => $qty,
          'unit_price' => $unitPrice
      ]
  ]);


  ## sale.line.added
- **When**: A line item is added to a draft sale.
- **Metadata**:
  - `sale_id` (int)
  - `line_id` (int)
  - `item_id` (int)
  - `qty` (float)
  - `unit_price` (float)
  - `line_discount` (float)   // added
  - `line_total` (float)       // optional, could be computed as (qty*unit_price) - line_discount
- **Example**:
  ```php
  Auditor::log('sale.line.added', [
      'entity_id' => $saleId,
      'meta' => [
          'line_id' => $lineId,
          'item_id' => $itemId,
          'qty' => $qty,
          'unit_price' => $unitPrice,
          'line_discount' => $lineDiscount
      ]
  ]);


  ## sale.posted
- **When**: A draft sale is successfully posted (status changes to POSTED).
- **Required metadata**:
  - `sale_id` (int)
  - `subtotal` (float)
  - `discount_total` (float)
  - `grand_total` (float)
  - `line_count` (int)
  - `pos_register_id` (int, may be null)
  - `or_series_id` (int, may be null)
- **Example**:
  ```php
  Auditor::log('sale.posted', [
      'entity_id' => $saleId,
      'meta' => [
          'subtotal' => 1500.00,
          'discount_total' => 100.00,
          'grand_total' => 1400.00,
          'line_count' => 3,
          'pos_register_id' => 2,
          'or_series_id' => 1
      ]
  ]);


  ## sale.or_issued
- **When**: An OR number is issued for a posted sale.
- **Required metadata**:
  - `sale_id` (int)
  - `or_series_id` (int)
  - `issued_or_no` (int)
  - `previous_current_no` (int)
  - `new_current_no` (int)
  - `note` (string) optional
- **Example**:
  ```php
  Auditor::log('sale.or_issued', [
      'entity_id' => $saleId,
      'meta' => [
          'or_series_id' => 1,
          'issued_or_no' => 101,
          'previous_current_no' => 101,
          'new_current_no' => 102,
          'note' => 'OR reserved/assigned (no print yet)'
      ]
  ]);