# E-Commerce

Full-featured e-commerce platform built with **Laravel 13**, featuring role-based access for **Admin** and **Customer**, integrated with **Midtrans** payment gateway, coupon system, PDF invoice generation, admin analytics dashboard, and notification system.

Built with Blade + Tailwind CSS 3 + Alpine.js frontend stack.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13, PHP ^8.3 |
| Frontend | Blade, Tailwind CSS 3, Alpine.js, Vite 6 |
| Auth | Laravel Breeze (Blade stack) |
| Database | SQLite (dev/test), MySQL (production-ready) |
| Payment | Midtrans Snap (credit card, bank transfer, e-wallet) + COD |
| PDF | barryvdh/laravel-dompdf ^3.1 |
| Queue | Database driver |
| Testing | Pest ^4.7 with RefreshDatabase |
| Code Style | Laravel Pint ^1.27 |

---

## Business Process / Alur Sistem

### Customer Flow

```
Register → Browse/Search Products → Manage Cart → Apply Coupon →
Checkout (Midtrans / COD) → Payment → Order Confirmation →
Download Invoice (PDF) → Write Review
```

### Admin Flow

```
Login → Dashboard Analytics → Manage:
  ├── Products (CRUD + Images + Stock)
  ├── Categories (CRUD)
  ├── Brands (CRUD)
  ├── Coupons (CRUD)
  └── Sliders (CRUD)
→ Process Orders (Pending → Paid → Processing → Shipped → Completed)
→ Download Shipping Label (PDF)
→ Monitor Reviews, Customers, Activity Logs, Notifications
```

### Order Status Workflow

```
Pending → Processing → Shipped → Completed
   ↘ Cancelled (any pre-paid stage)
```

### Payment Lifecycle (`payment_status`)

```
unpaid → pending → paid (settlement / capture+accept)
                → failed (deny / cancel / expire — stock restored)
                → challenge (fraud review — perlu review manual)
```

Semua transisi dipusatkan di **`PaymentStateService::apply()`** (idempoten, dipakai webhook, sync halaman order, dan reconcile).

### Payment Flow

1. Customer checks out via **Midtrans Snap** (bayar lewat halaman Midtrans) atau **COD** — stok dipotong + order dibuat `pending`/`unpaid`
2. Midtrans mengirim callback POST ke **`/api/payment/notification`** (signature-verified, throttled 30/menit)
3. Server verifikasi signature: `hash('sha512', order_id . status_code . gross_amount . server_key)` (tanpa separator)
4. Status diterapkan lewat `PaymentStateService` — transisi idempoten, tiap notifikasi unik dicatat ke tabel `payments` (dedupe via unique index)
5. Gagal/expired → stok direstok; ternyata terbayar setelah order dicancel → stok dipotong lagi + notifikasi anomaly
6. Order unpaid otomatis expire setelah **10 menit** (`MIDTRANS_EXPIRE_MINUTES`) via command `orders:reconcile` (cron)
7. Halaman order memantau status via polling (60×5 detik) + sync guard 10 detik ke gateway
8. Deployment: set **Notification URL** di dashboard Midtrans ke `https://domain-anda/api/payment/notification`

---

## Features

### Admin Features

| Feature | Status | Notes |
|---------|--------|-------|
| Dashboard Analytics | ✅ | 12 stat cards, ApexCharts (yearly/weekly/last week), best selling products, recent orders |
| Product Management | ✅ | CRUD, multiple images, stock management, low-stock notification |
| Category Management | ✅ | CRUD + activity log |
| Brand Management | ✅ | CRUD + activity log |
| Coupon Management | ✅ | CRUD + activity log |
| Slider Management | ✅ | CRUD + activity log |
| Order Management | ✅ | Status workflow, validasi kombinasi state, stok restore/re-deduct aman (lock + clamp) |
| Shipping Label | ✅ | PDF download (DomPDF) |
| Customer List | ✅ | Read-only |
| Review List | ✅ | Read-only |
| Activity Log | ✅ | Read-only, manual logging |
| Notification System | ✅ | Real-time dropdown, AJAX mark-as-read, cleanup command |
| Settings | ✅ | Basic settings page |

### Customer Features

| Feature | Status | Notes |
|---------|--------|-------|
| Register / Login | ✅ | Laravel Breeze (Blade), email verification |
| Home Page | ✅ | Hero slider, categories, hot deals, featured products |
| Product Listing | ✅ | Filter, sort, pagination |
| Product Detail | ✅ | Images, reviews, rating |
| Product Search | ✅ | AJAX with debounce 180ms, limit 8 results |
| Shopping Cart | ✅ | Add, update qty, remove, coupon integration |
| Wishlist | ✅ | Add, remove |
| Coupon System | ✅ | Apply/remove, minimum purchase, expiry, **sekali pakai per user** (unique index) |
| Checkout | ✅ | Midtrans Snap + COD, stock check with `lockForUpdate`, idempotency token |
| Order History | ✅ | List + detail, cancel pending orders, **Order Again** setelah pembayaran gagal |
| PDF Invoice | ✅ | Download via DomPDF, ownership check |
| Address Management | ✅ | CRUD with full address fields |
| Product Review | ✅ | Rating 1-5, comment, hanya untuk order completed yang benar miliknya |
| Dashboard | ✅ | Stat cards, recent orders, notifications |

---

## Kelebihan

- **Role-based authorization** — Middleware `role` memisahkan akses Admin dan Customer dengan redirect otomatis (tanpa loop untuk user tanpa role)
- **Idempotent payment state machine** — `PaymentStateService::apply()` satu-satunya tempat transisi status bayar (webhook/sync/reconcile), dedupe baris `payments`, log & notifikasi mismatch hanya sekali per order
- **Race condition handling** — Stock check menggunakan `lockForUpdate()` di dalam database transaction (checkout, cancel, webhook, admin update)
- **Order item snapshots** — Data produk, kategori, brand di-snapshot ke `order_items` saat checkout, histori tetap akurat walau data produk berubah
- **Admin notification system** — Notifikasi real-time (order, payment, stock, review) dengan AJAX mark-as-read + cleanup otomatis
- **PDF generation** — Invoice untuk customer + shipping label untuk admin via DomPDF
- **AJAX product search** — Live search dengan debounce 180ms, tampilkan gambar + nama + harga (input tidak-string ditolak aman)
- **Admin dashboard** — 12 stat cards, ApexCharts interaktif (3 periode), best selling products, revenue growth
- **Coupon system** — Validasi kode, expired, minimum purchase, session-based, **sekali pakai per user** (dicek di apply + checkout + unique index DB)
- **Query optimization** — Eager loading, `withCount`, pagination pada semua list
- **Global flash messages** — Partial `components/flash` di layout customer: success/error/validasi selalu terlihat

---

## Kekurangan / Known Issues

| Issue | Severity | Detail |
|-------|----------|--------|
| Tidak ada email notification | 🟡 Sedang | Customer tidak mendapat email konfirmasi order |
| Shipping cost hardcoded 0 | 🟢 Ringan | Belum ada integrasi API ongkir |
| VAT hardcoded 1000 | 🟢 Ringan | Harusnya configurable / percentage-based |
| Discount scheduler belum jalan | 🟡 Sedang | Fitur discount terjadwal belum diimplementasikan |
| Address tanpa `type` dan `is_default` | 🟢 Ringan | BRIEF menyebutkan tipe Rumah/Kantor/Kos |
| Activity log masih manual | 🟡 Sedang | Belum pakai event/listener/observer |
| Belum ada soft delete product | 🟢 Ringan | Data safety |
| Guest cart/wishlist butuh login | 🟢 Ringan | Halaman `/cart` & `/wishlist` di-`auth` (redirect ke login) |

Issue lama yang **sudah diperbaiki**: register tanpa role, kupon unlimited, profile routes dikomentari,
`route('dashboard')` 500, IDOR wishlist/review, stok dobel-restore, dead-end pembayaran gagal (kini ada "Order Again"),
flash message hilang, polling order berhenti terlalu cepat.

---

## Installation

### Prerequisites

- PHP ^8.3
- Composer
- Node.js & npm
- SQLite (default) or MySQL

### Step by Step

```bash
# 1. Clone repository
git clone <repository-url>
cd e-commerce-complex

# 2. Install PHP dependencies
composer install

# 3. Install frontend dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Create SQLite database (if using SQLite)
touch database/database.sqlite

# 7. Run migrations and seeders
php artisan migrate --seed

# 8. Build frontend assets
npm run build

# 9. Start development server
php artisan serve
```

### All-in-One Setup

```bash
composer run setup
```

This runs: copy `.env` → generate key → migrate with seed → install npm → build.

### Development Servers

```bash
composer run dev
```

Runs concurrently: `php artisan serve`, `queue:listen`, `pail`, and `npm run dev`.

### Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@gmail.com | password |
| Customer | customer@gmail.com | password |

### Midtrans Configuration

Fill these in `.env` (get keys from [Midtrans Dashboard](https://dashboard.midtrans.com)):

```ini
MERCHANT_ID=your_merchant_id
CLIENT_KEY=your_client_key
SERVER_KEY=your_server_key
# MIDTRANS_EXPIRE_MINUTES=10   (auto-expire order yang belum bayar, dalam menit)
```

### Running Tests

```bash
composer run test
# or
php artisan test
# or
php vendor/bin/pest
```

---

## Project Structure

```
app/
├── Console/Commands/        # orders:reconcile, cleanup commands
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # 12 admin controllers
│   │   ├── Customer/        # 13 customer controllers
│   │   └── Auth/            # 9 auth controllers (Breeze)
│   └── Middleware/          # RoleMiddleware
├── Models/                  # 21 Eloquent models
├── Providers/               # Service providers
└── Services/                # MidtransService, PaymentStateService,
                             # NotificationService, LogActivityService

database/
├── factories/               # 8 factories
├── migrations/              # 28 migrations
└── seeders/                 # DatabaseSeeder (idempotent)

resources/views/
├── admin/                   # Admin views (orders, products, etc.)
├── customer/                # Customer views (cart, checkout, etc.)
├── components/              # Blade components (termasuk flash global)
├── layouts/                 # App layouts (admin, app, guest)
└── auth/                    # Auth views (Breeze)

routes/
├── web.php                  # 95+ named routes
├── auth.php                 # Auth routes (Breeze)
└── console.php              # Scheduler

tests/                       # 27 test files (Pest) — 97 tests
planning/                    # Project documentation (BRIEF, ERD, etc.)
template/                    # HTML design mockups (reference only)
```

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
