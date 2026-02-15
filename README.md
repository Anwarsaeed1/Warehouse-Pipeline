# Warehouse Pipeline API

A **Laravel API** for multi-warehouse inventory management: warehouses, inventory items, stock levels, and stock transfers between warehouses. Includes authentication (Sanctum), role-based permissions, optional real-time low-stock alerts (Reverb), and cached warehouse inventory.

---

## Table of Contents

- [What This Project Does](#what-this-project-does)
- [Tech Stack](#tech-stack)
- [Architecture & Structure](#architecture--structure)
- [Design Patterns](#design-patterns)
- [Request Flow](#request-flow)
- [Stock Transfer Flow](#stock-transfer-flow)
- [Low Stock Alert Flow](#low-stock-alert-flow)
- [Caching](#caching)
- [API Reference](#api-reference)
- [Installation](#installation)
- [Postman & Reverb](#postman--reverb)

---

## What This Project Does

| Area | Description |
|------|-------------|
| **Auth** | Login (email/password), logout. Token-based API auth via Laravel Sanctum. |
| **Warehouses** | List warehouses with pagination and filters (name, code, location, is_active). |
| **Inventory items** | List products/items with pagination and filters (search, price range, is_active). |
| **Stocks** | Per-warehouse, per-item quantity and reserved quantity. List all stocks or one warehouse’s inventory; update quantity/reserved. |
| **Stock transfers** | Move quantity from one warehouse to another. Validates availability, updates both stocks, creates transfer record, invalidates cache, and may trigger low-stock logic. |
| **Low stock** | When stock falls at or below a threshold (per item or config), an event is fired: email notification (queued) and optional real-time broadcast (Reverb). |

---

## Tech Stack

- **PHP 8.2+**, **Laravel 11**
- **Laravel Sanctum** – API authentication
- **Spatie Laravel Permission** – roles and permissions
- **Spatie Laravel Translatable** – translatable attributes (e.g. role names)
- **Laravel Reverb** – WebSocket server for real-time (e.g. low-stock)
- **Pusher PHP Server** – Reverb uses Pusher protocol
- **Jenssegers/Date** – date helpers (used in app helpers)
- **MySQL** (or any Laravel-supported DB)

---

## Architecture & Structure

High-level layout:

```text
app/
├── Enum/                    # Enums (e.g. ActiveTypeEnum, StockTransferStatusEnum)
├── Events/                  # Domain events (e.g. LowStockDetected)
├── Exceptions/              # Custom exceptions (e.g. InsufficientStockException)
├── Filters/                 # Query filters (Pipeline pattern)
│   └── Inventory/           # Warehouse, Stock, InventoryItem filters
├── Helpers/                 # Global helpers (App.php: successResponse, fetchData, generateTransferNumber, etc.)
├── Http/
│   ├── Controllers/API/     # API controllers
│   │   ├── Auth/            # Login, logout
│   │   └── Inventory/       # Warehouses, stocks, items, stock-transfers
│   ├── Middleware/          # e.g. LanguageMiddleware
│   ├── Requests/            # Form requests (validation)
│   │   ├── Auth/
│   │   ├── Global/Other/    # PageRequest (page, pageSize)
│   │   ├── Inventory/       # WarehouseListRequest, InventoryPageRequest, UpdateStockRequest
│   │   └── StockTransfer/
│   └── Resources/           # API resources (JSON shape)
│       ├── Auth/
│       └── Inventory/       # Warehouse, Stock, InventoryItem, StockTransfer resources
├── Listeners/               # Event listeners (e.g. SendLowStockNotification)
├── Mail/                    # Mailable classes (e.g. LowStockMail)
├── Models/                  # Eloquent models (User, Warehouse, InventoryItem, Stock, StockTransfer, Role, Permission, …)
├── Observers/               # Model observers (e.g. UserObserver)
├── Policies/Inventory/      # Warehouse, InventoryItem, Stock, StockTransfer policies
├── Providers/               # AppServiceProvider (policies, Sanctum, event–listener binding)
├── Services/
│   ├── Auth/                # LoginService
│   ├── Global/              # RoleService, UploadService
│   └── Inventory/           # StockTransferService (transfer logic)
├── Trait/Global/            # EnumMethods, CreatedByObserver, etc.
└── Scopes/                  # Query scopes (e.g. UserScopes, RoleScopes)

config/
├── auth.php                 # Guards (sanctum), providers
├── inventory.php            # low_stock_threshold
├── broadcasting.php         # Reverb (and other drivers)
└── reverb.php               # Reverb server/app config

lang/                        # en & ar (api, auth, validation, pagination, etc.)
routes/
├── api.php                  # All API routes (auth + protected inventory routes)
├── channels.php             # Broadcast channel auth
└── web.php                  # Web routes (e.g. welcome)

Postman/
└── Warehouse-Pipeline-API.postman_collection.json   # Ready-to-import API collection
```

---

## Design Patterns

| Pattern | Where it’s used | Purpose |
|--------|-----------------|--------|
| **Service layer** | `StockTransferService` | Encapsulates transfer logic: lock stock, validate, move quantity, create transfer, cache invalidation, events. Keeps controller thin. |
| **Pipeline (middleware-style)** | List endpoints (warehouses, inventory, stocks, items) | Request passes through a list of **filters**; each filter optionally adds `where` (or similar) to the query. Keeps filtering modular and reusable. |
| **Form Request** | All API actions that accept input | Validation and authorization live in dedicated request classes (e.g. `StockTransferRequest`, `WarehouseListRequest`, `InventoryPageRequest`). |
| **API Resource** | All JSON responses | `WarehouseResource`, `StockResource`, `InventoryItemResource`, `StockTransferResource` define the response shape and `whenLoaded` for relations. |
| **Policy (authorization)** | Routes with `->can(...)` | `WarehousePolicy`, `InventoryItemPolicy`, `StockPolicy`, `StockTransferPolicy` gate access (viewAny, view, create, update, etc.) per model. |
| **Event / Listener** | Low stock | `LowStockDetected` event; `SendLowStockNotification` listener (queued) sends email. Decouples “stock went low” from “notify someone”. |
| **Custom exception** | Stock transfer | `InsufficientStockException` for “quantity exceeds available”; can be mapped to HTTP 422 in exception handler. |
| **Repository-style encapsulation** | Inside `StockTransferService` | No generic repository interface; the service encapsulates “lock source stock”, “get or create destination stock”, “create transfer record”, “invalidate cache”, “dispatch events”. |

---

## Request Flow

Typical flow for a protected API call:

1. **HTTP request** → `routes/api.php` (e.g. `POST /api/stock-transfers`).
2. **Middleware** → `auth:sanctum` (and any global API middleware, e.g. language).
3. **Route** → Controller method + `->can('create', StockTransfer::class)` (policy check).
4. **Controller** → Receives **Form Request** (validation runs); calls **Service** or builds query with **Pipeline** (filters).
5. **Service / Query** → Business logic (e.g. `StockTransferService::transfer`) or filtered/paginated query.
6. **Response** → **API Resource** (e.g. `StockTransferResource`) → JSON (`successResponse()` helper).

For list endpoints (e.g. `GET /api/warehouses`, `GET /api/inventory`):

- **Form Request** validates query params (page, pageSize, filters).
- **Pipeline** sends the Eloquent query through **filters** (e.g. `WarehouseNameFilter`, `WarehouseIsActiveFilter`).
- **Helper** `fetchData($query, $pageSize, ResourceClass)` paginates and returns resource collection.

---

## Stock Transfer Flow

Flow for `POST /api/stock-transfers` (create a transfer):

1. **Request** – `StockTransferRequest` validates `from_warehouse_id`, `to_warehouse_id`, `inventory_item_id`, `quantity`, optional `note`; custom rule ensures quantity ≤ available (source stock).
2. **Policy** – User must pass `create` on `StockTransfer`.
3. **Controller** – Calls `StockTransferService::transfer($request->validated(), auth()->id())`.
4. **Service** (inside a **DB transaction**):
   - **Lock source stock** – `Stock` for `(from_warehouse_id, inventory_item_id)` with `lockForUpdate()`.
   - **Validate** – Available quantity (`quantity - reserved_quantity`) ≥ requested quantity; else throw `InsufficientStockException`.
   - **Decrement** source stock `quantity`.
   - **Get or create** destination stock for `(to_warehouse_id, inventory_item_id)`; **increment** its `quantity`.
   - **Create** `StockTransfer` (transfer number, status Completed, `completed_at`, `transferred_by`).
   - **Invalidate cache** – increment version for both warehouses (`warehouse.inventory.{id}.version`).
   - **After commit** – `DB::afterCommit()`: refresh source and destination stock; if either’s quantity ≤ low-stock threshold, dispatch `LowStockDetected` for that stock.
   - **Return** transfer with relations loaded (`fromWarehouse`, `toWarehouse`, `inventoryItem`, `transferredBy`).
5. **Response** – `StockTransferResource` (IDs + nested warehouse, item, transferred-by user when loaded).

---

## Low Stock Alert Flow

When stock quantity is at or below the threshold (item’s `low_stock_threshold` or `config('inventory.low_stock_threshold')`):

1. **Trigger** – Inside `StockTransferService`, after the transaction commits, for source and/or destination stock.
2. **Event** – `LowStockDetected` is dispatched with `Stock` model.
3. **Listener** – `SendLowStockNotification` (queued):
   - Loads warehouse and inventory item on the stock.
   - Sends `LowStockMail` to configured admin email.
4. **Broadcasting** – Same event implements `ShouldBroadcast`; payload is sent to Reverb channel `low-stock` as event `low-stock.detected` (for real-time UIs).

Event–listener binding is in `AppServiceProvider`: `Event::listen(LowStockDetected::class, SendLowStockNotification::class)`.

---

## Caching

- **Warehouse inventory** – List for a single warehouse (`GET /api/warehouses/{id}/inventory`) is cached with a **version key**: `warehouse.inventory.{id}.v{version}.{hash}`. The hash includes request params (e.g. search, price_min, price_max, page, pageSize, item_is_active).
- **Invalidation** – On stock transfer, `Cache::increment("warehouse.inventory.{id}.version")` is called for both source and destination warehouse, so all cached pages for that warehouse are effectively invalidated.
- **TTL** – Cached result is stored with a fixed duration (e.g. 5 minutes); version bump makes old cache unused even before TTL.

---

## API Reference

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | Login (email, password). Returns token. |
| POST | `/api/logout` | Logout (Bearer token). |
| GET | `/api/warehouses` | List warehouses (paginated). Filters: `page`, `pageSize`, `name`, `code`, `location`, `is_active` (0/1). |
| GET | `/api/warehouses/{id}/inventory` | Cached inventory for one warehouse. Filters: `page`, `pageSize`, `search`, `price_min`, `price_max`, `item_is_active` (0/1). |
| GET | `/api/inventory` | List stocks (all warehouses). Filters: `page`, `pageSize`, `warehouse_id`, `warehouse_is_active`, `item_is_active`, `search`, `price_min`, `price_max`. |
| GET | `/api/inventory/items` | List inventory items. Filters: `page`, `pageSize`, `search`, `price_min`, `price_max`, `is_active` (0/1). |
| PUT | `/api/stocks/{id}` | Update stock (`quantity`, `reserved_quantity`). |
| POST | `/api/stock-transfers` | Create stock transfer (`from_warehouse_id`, `to_warehouse_id`, `inventory_item_id`, `quantity`, `note`). |

- All list endpoints support **pagination** via `page` and `pageSize`.
- Filter parameters are optional.
- Protected routes require `Authorization: Bearer {token}` and the corresponding policy ability.

---

## Installation

**Prerequisites:** PHP 8.2+, Composer, MySQL (or compatible DB).

1. Clone and install dependencies:
   ```bash
   composer install
   ```
2. Environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Configure `.env` (DB, `BROADCAST_CONNECTION=reverb` if using Reverb, Reverb app keys/host/port, mail).
4. Migrate and seed:
   ```bash
   php artisan migrate --seed
   ```
5. Run the app:
   ```bash
   php artisan serve
   ```
6. Optional: queue worker for low-stock email:
   ```bash
   php artisan queue:work
   ```
7. Optional: Reverb for real-time:
   ```bash
   php artisan reverb:start
   ```

---

## Postman & Reverb

- **Postman** – Import `Postman/Warehouse-Pipeline-API.postman_collection.json`. Set collection variables: `base_url`, `token` (after login). All list endpoints include query params for pagination and filters.
- **Reverb** – Set `BROADCAST_CONNECTION=reverb` and Reverb env vars in `.env`. Subscribe to channel `low-stock` and listen for event `low-stock.detected` to receive real-time low-stock payloads.

---

## License

MIT.
