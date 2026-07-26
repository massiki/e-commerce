# Progress Proyek E-Commerce Complex

**Terakhir diperbarui:** 26 Juli 2026

---

## Ringkasan Progress

| Komponen | Progress |
|----------|----------|
| Database Schema | ✅ ~100% (23 migrations) |
| Models | ✅ ~100% (20 models: 3 `#[Fillable]`, 6 `#[Guarded]`, 4 `$fillable`, 1 `$guarded`, 6 tanpa) |
| Controllers | ⬜ ~90% (30 total: 10 admin, 10 customer, 9 auth, 1 base) |
| Views (Blade) | ⬜ ~72% (78 file) |
| Routes | ⬜ ~88% (85+ named routes) |
| Tests | ⬜ ~10% (11 files, semua Breeze default) |
| **Estimasi Keseluruhan** | **⬜ ~72%** |

---

## ✅ Sudah Selesai

### Authentication & Authorization
- Laravel Breeze (login, register, password reset, email verification)
- Role-based routing (`role:admin`, `role:customer`)
- `RoleMiddleware` — registered as `role` alias in `bootstrap/app.php`
- Login redirect by role (`AuthenticatedSessionController`)
- 2 roles seeded: admin & customer
- 2 users seeded: admin@gmail.com & customer@gmail.com

### Database Schema (23 Migration Files)
| # | File | Table |
|---|------|-------|
| 1 | `create_users_table` | `users`, `password_reset_tokens`, `sessions` |
| 2 | `create_cache_table` | `cache`, `cache_locks` |
| 3 | `create_jobs_table` | `jobs`, `job_batches`, `failed_jobs` |
| 4 | `create_roles_table` | `roles` |
| 5 | `add_role_id_and_phone_to_users_table` | **Alter** `users` (+ `role_id`, `phone`) |
| 6 | `create_categories_table` | `categories` |
| 7 | `create_brands_table` | `brands` |
| 8 | `create_sliders_table` | `sliders` |
| 9 | `create_coupons_table` | `coupons` |
| 10 | `create_products_table` | `products` |
| 11 | `create_product_images_table` | `product_images` |
| 12 | `create_discounts_table` | `discounts` |
| 13 | `create_addresses_table` | `addresses` |
| 14 | `create_carts_table` | `carts` |
| 15 | `create_wishlists_table` | `wishlists` |
| 16 | `create_cart_items_table` | `cart_items` |
| 17 | `create_wishlist_items_table` | `wishlist_items` |
| 18 | `create_coupon_usages_table` | `coupon_usages` |
| 19 | `create_notifications_table` | `notifications` |
| 20 | `create_activity_logs_table` | `activity_logs` |
| 21 | `create_orders_table` | `orders` |
| 22 | `create_order_items_table` | `order_items` |
| 23 | `create_reviews_table` | `reviews` |

### Seeders
- `DatabaseSeeder` — seeds: 2 roles, 2 users, 5 brands, 5 categories, 50 products
- Factories: `UserFactory`, `CategoryFactory`, `BrandFactory`, `ProductFactory`, `DiscountFactory`, `ReviewFactory`

### Models (20 total)
| Status | Models |
|--------|--------|
| ✅ PHP 8 Attributes (`#[Fillable]`) | Cart, CartItem, User |
| ✅ PHP 8 Attributes (`#[Guarded]`) | Discount, Product, ProductImage, Review, Wishlist, WishlistItem |
| ✅ Traditional `$fillable` | Brand, Category, Coupon, Slider, ActivityLog |
| ⬜ Tanpa mass assignment protection | Address, CouponUsage, Notification, Order, OrderItem, Role |

### Admin CRUD — Complete (Controller + Views + Routes + ActivityLog)
| Fitur | Controller | Index | Create | Edit | Store | Update | Destroy | ActivityLog |
|-------|-----------|-------|--------|------|-------|--------|---------|-------------|
| **Brand** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ store/update/destroy |
| **Category** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ store/update/destroy |
| **Coupon** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ store/update/destroy |
| **Product** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ store/update/destroy |
| **Slider** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ store/update/destroy |

### Customer Area — Public Routes (No Auth)
| Fitur | Controller | Routes | Status |
|-------|-----------|--------|--------|
| **Home** | `HomeController::index()` | `GET /` → `home` | ✅ |
| **Product Listing** | `ProductController::index()` | `GET /products` → `products.index` | ✅ |
| **Product Detail** | `ProductController::show()` | `GET /products/{product:slug}` → `products.show` | ✅ |
| **Cart View** | `CartController::index()` | `GET /cart` → `cart.index` | ✅ |
| **Wishlist View** | `WishlistController::index()` | `GET /wishlist` → `wishlist.index` | ✅ |
| **Product Search (AJAX)** | `SearchController::search()` | `GET /products/search` → `products.search` | ✅ |

### Customer Area — Authenticated Routes (role:customer)
| Fitur | Controller | Routes | Status |
|-------|-----------|--------|--------|
| **Dashboard** | Closure | `GET /customer/dashboard` → `customer.dashboard` | ⬜ View statis |
| **Cart Add** | `CartController::store()` | `POST /customer/cart/add/{product}` | ✅ |
| **Cart Update** | `CartController::update()` | `PATCH /customer/cart/update` | ✅ |
| **Cart Remove** | `CartController::destroy()` | `DELETE /customer/cart/remove/{cartItem}` | ✅ |
| **Wishlist Add** | `WishlistController::store()` | `POST /customer/wishlist/add/{product}` | ✅ |
| **Wishlist Remove** | `WishlistController::remove()` | `DELETE /customer/wishlist/remove/{wishlistItem}` | ✅ |
| **Coupon Apply** | `CouponController::apply()` | `POST /customer/coupon/apply` | ✅ |
| **Coupon Remove** | `CouponController::remove()` | `DELETE /customer/coupon/remove` | ✅ |

### Customer Coupon System
- `CouponController@apply()` — validasi kode, expired, minimum purchase, simpan ke session
- `CouponController@remove()` — hapus coupon dari session
- `CartController@index()` — validasi ulang expired & minimum purchase setiap load halaman
- View: form APPLY/REMOVE kondisional, tampilkan discount row, flash messages error/sukses
- Server-side session (bukan localStorage)

### Customer AJAX Search
- `SearchController::search()` — return JSON produk by name `LIKE`
- Route `GET /products/search` terdaftar SEBELUM `products/{slug}`
- Input minimal 2 karakter, debounce 180ms, limit 8 hasil
- Tampilkan gambar + nama + harga di dropdown hasil pencarian
- Override JavaScript di `layouts/app.blade.php` (setelah theme.js)

### Cart Improvements
- Nested form bug fixed: form coupon dipisah dari form update cart
- Coupon form menggunakan `route('customer.coupon.apply')` dengan method POST
- Tombol UPDATE CART menggunakan atribut `form="cart-form"`

### Customer Checkout (Midtrans + COD)
- `CheckoutController@index` — tampilkan cart + addresses + coupon + total
- `CheckoutController@store` — validasi, stock check (`lockForUpdate` dalam transaction), create order + order items, cart cleanup, coupon usage, Snap token generation SEBELUM transaction
- `CheckoutController@confirmation` — tampilkan order confirmation, Pay Now button (Midtrans) / COD instructions
- `MidtransController@handleCallback` — signature verification (`hash('sha512')`), stock restore on payment failure (deny/cancel/expire), payment status update
- `config/midtrans.php` — merchant_id, client_key, server_key via env
- Package `midtrans/midtrans-php` ^2.6.2 terinstall
- View: `customer/checkout.blade.php`, `customer/order-confirmation.blade.php`
- SweetAlert confirmation before place order
- Address selection via dropdown with JS auto-fill
- Midtrans Snap: onSuccess redirect ke order show, onError alert, onPending ke order show

### Admin Order Management
- `OrderController@index` — list all orders, search by invoice/recipient/phone
- `OrderController@show` — detail order + items
- `OrderController@update` — update status + payment_status, stock restore on cancel
- Views: `admin/orders/index.blade.php`, `admin/orders/show.blade.php`

### Customer Orders & Addresses
- `Customer\OrderController@index` — list orders with pagination, items count
- `Customer\OrderController@show` — order detail, items, transaction, cancel button
- `Customer\OrderController@cancel` — cancel order + stock restore
- `Customer\AddressController@index` — list addresses
- `Customer\AddressController@create` + `store` — add address
- `Customer\AddressController@edit` + `update` — edit address
- `Customer\AddressController@destroy` — delete address

### Checkout Critical Fixes
- **Race condition stock check** — pindah ke dalam `DB::transaction` + `lockForUpdate()` ✅
- **Stock restore on Midtrans payment failure** — deny/cancel/expire restore stock ✅
- **Stock restore on admin cancel** — `Admin/OrderController@update` restore stock saat `status === 'cancelled'` ✅
- **Midtrans callback signature verification** — `hash('sha512', order_id + status_code + gross_amount + '.' + server_key)` ✅
- **Exposed API keys** — hapus key asli dari `.env.example`, ganti placeholder komentar ✅
- **CartItem `sub_total` accessor bug** — `$this->product?->discount` → `$this->product?->has_discount` supaya diskon expired tidak terpakai ✅
- **Midtrans Snap gagal → order orphan** — generate Snap token SEBELUM `DB::transaction`, kalau gagal order tidak jadi dibuat ✅

### Routes (web.php) — 85+ Named Routes
- **Public/Guest (14):** `/`, register, login, password reset, `/products`, `/products/search`, `/products/{slug}`, `/cart`, `/wishlist`
- **Auth (7):** email verification, password confirm, password update, logout
- **Admin (~35):** prefix `admin/`, middleware `auth+role:admin`:
  - 5 full resource: brands, categories, products, coupons, sliders
  - 1 resource (partial): orders
  - 3 read-only: customers, reviews, activities
  - 1 setting: index + update
  - 1 dashboard
- **Customer auth (8):** prefix `customer/`, middleware `auth+role:customer`:
  - dashboard, cart CRUD (3), wishlist CRUD (2), coupon apply/remove (2)

---

## ⬜ Sebagian (Controller Parsial, View Terbatas)

### Admin — Controller hanya index saja (atau parsial)
| Fitur | Controller | View | Route | ActivityLog |
|-------|-----------|------|-------|-------------|
| Customers | `index()` saja | `index.blade.php` | GET only | ❌ |
| Reviews | `index()` saja | `index.blade.php` | GET only | ❌ |
| Activities | `index()` saja | `index.blade.php` | GET only | ❌ (read-only) |
| Settings | `index()` + `update()` | `index.blade.php` | GET + POST | ❌ |

### Customer Area — View Saja (Controller Belum / Masih Perlu)
| Fitur | View Files | Controller | Routes |
|-------|-----------|------------|--------|
| Dashboard | `customer/dashboard/index.blade.php` | ❌ (Closure) | Static view |
| Account | `customer/dashboard/account.blade.php` | ❌ | ❌ |
| History | `customer/dashboard/history.blade.php` | ❌ | ❌ |

---

## ❌ Belum Tersentuh Sama Sekali

| Fitur | Prioritas | Keterangan |
|-------|-----------|------------|
| Discount Scheduler | 🟡 Sedang | Butuh Laravel Task Scheduler + console command |
| Dashboard Analytics | 🟡 Sedang | View statis, belum connect ke database |
| Customer Dashboard Controller | 🟡 Sedang | 5 view sudah ada, controller belum |
| PDF Invoice | 🟡 Sedang | Butuh `barryvdh/laravel-dompdf` |
| Notification System | 🟡 Sedang | Belum ada `app/Notifications/` |
| Auto Rating Calculation | 🟢 Ringan | - |
| Low Stock Warning | 🟢 Ringan | - |
| Soft Delete Product | 🟢 Ringan | - |
| Activity Log Automation | 🟢 Ringan | Masih manual di controller |

---

## 🔧 Issues / Technical Debt

| Issue | Detail | Severity |
|-------|--------|----------|
| Register tidak assign role | User baru daftar dapat `role_id = null` | 🔴 Tinggi |
| 3 model tanpa mass assignment protection | Address, CouponUsage, Notification (Order & OrderItem masih `#[Guarded([])]`) | 🔴 Tinggi |
| Route profile di-comment | `routes/web.php` — profile edit/update/destroy tidak aktif | 🟡 Sedang |
| Dashboard masih statis | Belum ada query real dari database | 🟡 Sedang |
| Customer dashboard controller | 5 view sudah ada, controller + routes belum dibuat | 🟡 Sedang |
| Coupon tidak punya `max_uses` / `usage_limit` | Bisa dipakai unlimited tanpa batas | 🟡 Sedang |
| Tidak ada email/notifikasi setelah order | Customer tidak dapat konfirmasi order lewat email | 🟡 Sedang |
| Activity log manual | Belum pakai event/listener atau observer | 🟡 Sedang |
| Admin Brand/Category/Product `show()` | Method `show()` ada tapi isinya kosong (stub) | 🟢 Ringan |
| `onPending` Midtrans redirect ke `#` | Harusnya redirect ke order show page | 🟢 Ringan |
| `shipping_cost` hardcoded 0 | Tidak ada shipping API integration | 🟢 Ringan |
| VAT hardcoded 1000 | Harusnya configurable / percentage-based | 🟢 Ringan |
| Address tidak punya `type` (Rumah/Kantor/Kos) & `is_default` | Disebut di BRIEF | 🟢 Ringan |
| Discount row duplicate `id="cart-vat"` | ID sama dengan VAT row, tidak ada JS yang pakai | 🟢 Ringan |

---

## Rekomendasi Prioritas Pengembangan

| Urutan | Fitur | Alasan |
|--------|-------|--------|
| 1 | **Assign role saat register** | User baru dapat `role_id = null`, perlu di-fix |
| 2 | **Dashboard Analytics (Admin & Customer)** | Query real dari database, ganti view statis |
| 3 | **Customer Dashboard Controller** | Account, history — 3 view sudah siap, controller belum |
| 4 | **Notification System** | Notifikasi order created, payment success, shipped, etc |
| 5 | **Discount Scheduler** | Otomatisasi diskon (start/end, auto return price) |
| 6 | **Coupon max_uses** | Limitasi pemakaian kupon |
| 7 | **PDF Invoice** | Download invoice setelah payment |
| 8 | **Reviews & Rating** | Setelah order completed |
| 9 | **Midtrans `onPending` redirect** | Fix broken redirect |
| 10 | **Activity Log Automation** | Event/listener ganti manual |
| 11 | **Shipping cost integration** | API shipping / ongkir |
| 12 | **Low Stock Warning** | Monitoring stok |
| 13 | **Soft Delete Product** | Data safety |

---

## Detail Fitur per Modul (dari BRIEF.md)

### Admin
- [x] Login
- [x] CRUD Category
- [x] CRUD Brand
- [x] CRUD Coupon
- [x] CRUD Product (+ Multiple Images, Stock Management)
- [x] CRUD Slider
- [ ] Dashboard (Revenue, Orders, Customers, Products, Monthly Sales, Best Selling, Low Stock)
- [ ] Discount Scheduler (Start/End, Auto Return Normal Price)
- [x] Order Management (Pending → Processing → Completed/Cancelled)
- [ ] Customer Management (List, Detail, Purchase History)
- [ ] Review Management (View Reviews & Ratings)
- [ ] Activity Log (Login, Product, Order, Coupon)

### Customer
- [x] Register
- [x] Login
- [x] Home Page (Hero Slider, Categories, Hot Deals, Featured Products)
- [x] Browse Products (Filter, Sort, Detail)
- [x] Product Search (AJAX, debounce, live results)
- [x] Wishlist (View, Add, Remove)
- [x] Shopping Cart (View, Add, Update Qty, Remove)
- [x] Coupon (Apply, Validate, Remove)
- [/] Dashboard (Total Orders, Spending, Wishlist, Notifications, Payment History) — view statis
- [x] Address Management (CRUD + Recipient, Phone, Province, City, District, Postal Code, Full Address)
- [x] Checkout (Midtrans & COD)
- [x] Order Detail (Invoice, Status, Items, Total)
- [x] Payment History (via order list + detail)
- [ ] Product Review (Rating 1-5, Review setelah Completed)
- [ ] Notification (Order Created, Payment Success, Processed, Shipped, Completed, Coupon Applied)
- [ ] Invoice (Download PDF)

---

## Stack & Dependencies

### Terpasang
| Package | Version |
|---------|---------|
| `laravel/framework` | ^13.8 |
| `laravel/breeze` | ^2.4 |
| `pestphp/pest` | ^4.7 |
| `laravel/pint` | ^1.27 |
| `laravel/debugbar` | ^4.4 |
| `midtrans/midtrans-php` | ^2.6.2 |

### Perlu Ditambahkan
| Package | Untuk |
|---------|-------|
| `barryvdh/laravel-dompdf` | PDF invoice |
| `spatie/laravel-activitylog` (opsional) | Activity log otomatis |

---

## Total File Summary

```
app/Models/              → 20 models (3 tanpa mass assignment protection)
app/Http/Controllers/    → 30 controllers (10 admin: 4 partial; 10 customer: all complete; 9 auth; 1 base)
resources/views/         → 78 Blade files
database/migrations/     → 23 migrations
database/seeders/        → 1 seeder (DatabaseSeeder)
database/factories/      → 6 factories (+ ReviewFactory)
routes/                  → web.php, auth.php, console.php
tests/                   → 11 test files (semua Breeze default)
planning/                → 6 dokumen (BRIEF, ERD, USE_CASE, SEQUENCE, ACTIVITY, progres)
```
