# E-Commerce Complex

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
Pending → Paid → Processing → Shipped → Completed
                                  ↘ Cancelled (any stage)
```

### Payment Flow

1. Customer checks out via **Midtrans Snap** (redirect to Midtrans payment page) or **COD**
2. Midtrans sends callback POST to `/customer/midtrans/callback` (CSRF exempted)
3. Server verifies signature (`hash('sha512', order_id + status_code + gross_amount + '.' + server_key)`)
4. Payment status updated: `unpaid` → `paid` / `failed` / `expired`
5. On failure: stock restored automatically
6. Admin notified via in-app notification system

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
| Order Management | ✅ | Status workflow, stock restore on cancel |
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
| Coupon System | ✅ | Apply/remove, minimum purchase, expiry validation |
| Checkout | ✅ | Midtrans Snap + COD, stock check with `lockForUpdate` |
| Order History | ✅ | List + detail, cancel pending orders |
| PDF Invoice | ✅ | Download via DomPDF, ownership check |
| Address Management | ✅ | CRUD with full address fields |
| Product Review | ✅ | Rating 1-5, comment, triggers notification |
| Dashboard | ✅ | Stat cards, recent orders, notifications |

---

## Kelebihan

- **Role-based authorization** — Middleware `role` memisahkan akses Admin dan Customer dengan redirect otomatis
- **Midtrans payment integration** — Snap API + callback signature verification (`hash('sha512')`) + stock restore otomatis saat pembayaran gagal
- **Race condition handling** — Stock check menggunakan `lockForUpdate()` di dalam database transaction
- **Order item snapshots** — Data produk, kategori, brand di-snapshot ke `order_items` saat checkout, histori tetap akurat walau data produk berubah
- **Admin notification system** — Notifikasi real-time (order, payment, stock, review) dengan AJAX mark-as-read + cleanup otomatis
- **PDF generation** — Invoice untuk customer + shipping label untuk admin via DomPDF
- **AJAX product search** — Live search dengan debounce 180ms, tampilkan gambar + nama + harga
- **Admin dashboard** — 12 stat cards, ApexCharts interaktif (3 periode), best selling products, revenue growth
- **Coupon system** — Validasi kode, expired, minimum purchase, session-based
- **Query optimization** — Eager loading, `withCount`, pagination pada semua list

---

## Kekurangan / Known Issues

| Issue | Severity | Detail |
|-------|----------|--------|
| Register tidak assign role | 🔴 Tinggi | User baru daftar mendapat `role_id = null`, perlu di-fix |
| Coupon tanpa `max_uses` | 🟡 Sedang | Bisa dipakai unlimited tanpa batas |
| Tidak ada email notification | 🟡 Sedang | Customer tidak mendapat email konfirmasi order |
| Shipping cost hardcoded 0 | 🟢 Ringan | Belum ada integrasi API ongkir |
| VAT hardcoded 1000 | 🟢 Ringan | Harusnya configurable / percentage-based |
| Discount scheduler belum jalan | 🟡 Sedang | Console command + scheduling belum dibuat |
| Profile routes di-comment | 🟡 Sedang | Profile edit/update/destroy di `routes/web.php` tidak aktif |
| `onPending` Midtrans redirect ke `#` | 🟢 Ringan | Harusnya redirect ke order show |
| Address tanpa `type` dan `is_default` | 🟢 Ringan | BRIEF menyebutkan tipe Rumah/Kantor/Kos |
| Activity log masih manual | 🟡 Sedang | Belum pakai event/listener/observer |
| Belum ada soft delete product | 🟢 Ringan | Data safety |

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
├── Console/Commands/        # Artisan commands
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # 11 admin controllers
│   │   ├── Customer/        # 12 customer controllers
│   │   └── Auth/            # 9 auth controllers (Breeze)
│   └── Middleware/          # RoleMiddleware
├── Models/                  # 20 Eloquent models
├── Providers/               # Service providers
└── Services/                # NotificationService

database/
├── factories/               # 8 factories
├── migrations/              # 25 migrations
└── seeders/                 # DatabaseSeeder

resources/views/
├── admin/                   # Admin views (orders, products, etc.)
├── customer/                # Customer views (cart, checkout, etc.)
├── components/              # Blade components
├── layouts/                 # App layouts (admin, app, guest)
└── auth/                    # Auth views (Breeze)

routes/
├── web.php                  # 93+ named routes
├── auth.php                 # Auth routes (Breeze)
└── console.php              # Scheduler

tests/                       # 12 test files (Pest)
planning/                    # Project documentation (BRIEF, ERD, etc.)
template/                    # HTML design mockups (reference only)
```

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
