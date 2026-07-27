# Progress Proyek E-Commerce Complex

**Terakhir diperbarui:** 27 Juli 2026 (Sesi 3)

---

## Ringkasan Progress


| Komponen                 | Progress                                                                                   |
| ------------------------ | ------------------------------------------------------------------------------------------ |
| Database Schema          | ✅ ~100% (24 migrations)                                                                    |
| Models                   | ✅ ~100% (20 models: 3 `#[Fillable]`, 7 `#[Guarded]`, 4 `$fillable`, 1 `$guarded`, 5 tanpa) |
| Controllers              | ✅ ~95% (34 total: 11 admin, 12 customer, 9 auth, 2 base)                                   |
| Views (Blade)            | ✅ ~80% (83 file)                                                                           |
| Routes                   | ✅ ~95% (91+ named routes)                                                                  |
| Tests                    | ⬜ ~10% (11 files, semua Breeze default)                                                    |
| **Estimasi Keseluruhan** | **⬜ ~82%**                                                                                 |


---

## ✅ Sudah Selesai

### Authentication & Authorization

- Laravel Breeze (login, register, password reset, email verification)
- Role-based routing (`role:admin`, `role:customer`)
- `RoleMiddleware` — registered as `role` alias in `bootstrap/app.php`
- Login redirect by role (`AuthenticatedSessionController`)
- 2 roles seeded: admin & customer
- 2 users seeded: [admin@gmail.com](mailto:admin@gmail.com) & [customer@gmail.com](mailto:customer@gmail.com)



### Database Schema (24 Migration Files)


| #   | File                                       | Table                                        |
| --- | ------------------------------------------ | -------------------------------------------- |
| 1   | `create_users_table`                       | `users`, `password_reset_tokens`, `sessions` |
| 2   | `create_cache_table`                       | `cache`, `cache_locks`                       |
| 3   | `create_jobs_table`                        | `jobs`, `job_batches`, `failed_jobs`         |
| 4   | `create_roles_table`                       | `roles`                                      |
| 5   | `add_role_id_and_phone_to_users_table`     | **Alter** `users` (+ `role_id`, `phone`)     |
| 6   | `create_categories_table`                  | `categories`                                 |
| 7   | `create_brands_table`                      | `brands`                                     |
| 8   | `create_sliders_table`                     | `sliders`                                    |
| 9   | `create_coupons_table`                     | `coupons`                                    |
| 10  | `create_products_table`                    | `products`                                   |
| 11  | `create_product_images_table`              | `product_images`                             |
| 12  | `create_discounts_table`                   | `discounts`                                  |
| 13  | `create_addresses_table`                   | `addresses`                                  |
| 14  | `create_carts_table`                       | `carts`                                      |
| 15  | `create_wishlists_table`                   | `wishlists`                                  |
| 16  | `create_cart_items_table`                  | `cart_items`                                 |
| 17  | `create_wishlist_items_table`              | `wishlist_items`                             |
| 18  | `create_coupon_usages_table`               | `coupon_usages`                              |
| 19  | `create_notifications_table`               | `notifications`                              |
| 20  | `create_activity_logs_table`               | `activity_logs`                              |
| 21  | `create_orders_table`                      | `orders`                                     |
| 22  | `create_order_items_table`                 | `order_items`                                |
| 23  | `create_reviews_table`                     | `reviews`                                    |
| 24  | `add_type_and_data_to_notifications_table` | **Alter** `notifications` (+ `type`, `data`) |




### Seeders

- `DatabaseSeeder` — seeds: 2 roles, 2 users, 5 brands, 5 categories, 50 products
- Factories: `UserFactory`, `CategoryFactory`, `BrandFactory`, `ProductFactory`, `DiscountFactory`, `ReviewFactory`



### Models (20 total)


| Status                             | Models                                                                        |
| ---------------------------------- | ----------------------------------------------------------------------------- |
| ✅ PHP 8 Attributes (`#[Fillable]`) | Cart, CartItem, User                                                          |
| ✅ PHP 8 Attributes (`#[Guarded]`)  | Discount, Product, ProductImage, Review, Wishlist, WishlistItem, Notification |
| ✅ Traditional `$fillable`          | Brand, Category, Coupon, Slider, ActivityLog                                  |
| ⬜ Tanpa mass assignment protection | Address, CouponUsage, Order, OrderItem, Role                                  |




### Admin CRUD — Complete (Controller + Views + Routes + ActivityLog)


| Fitur        | Controller | Index | Create | Edit | Store | Update | Destroy | ActivityLog            |
| ------------ | ---------- | ----- | ------ | ---- | ----- | ------ | ------- | ---------------------- |
| **Brand**    | ✅          | ✅     | ✅      | ✅    | ✅     | ✅      | ✅       | ✅ store/update/destroy |
| **Category** | ✅          | ✅     | ✅      | ✅    | ✅     | ✅      | ✅       | ✅ store/update/destroy |
| **Coupon**   | ✅          | ✅     | ✅      | ✅    | ✅     | ✅      | ✅       | ✅ store/update/destroy |
| **Product**  | ✅          | ✅     | ✅      | ✅    | ✅     | ✅      | ✅       | ✅ store/update/destroy |
| **Slider**   | ✅          | ✅     | ✅      | ✅    | ✅     | ✅      | ✅       | ✅ store/update/destroy |




### Customer Area — Public Routes (No Auth)


| Fitur                     | Controller                    | Routes                                           | Status |
| ------------------------- | ----------------------------- | ------------------------------------------------ | ------ |
| **Home**                  | `HomeController::index()`     | `GET /` → `home`                                 | ✅      |
| **Product Listing**       | `ProductController::index()`  | `GET /products` → `products.index`               | ✅      |
| **Product Detail**        | `ProductController::show()`   | `GET /products/{product:slug}` → `products.show` | ✅      |
| **Cart View**             | `CartController::index()`     | `GET /cart` → `cart.index`                       | ✅      |
| **Wishlist View**         | `WishlistController::index()` | `GET /wishlist` → `wishlist.index`               | ✅      |
| **Product Search (AJAX)** | `SearchController::search()`  | `GET /products/search` → `products.search`       | ✅      |




### Customer Area — Authenticated Routes (role:customer)


| Fitur               | Controller                     | Routes                                              | Status        |
| ------------------- | ------------------------------ | --------------------------------------------------- | ------------- |
| **Dashboard**       | `DashboardController@index`    | `GET /customer/dashboard` → `customer.dashboard`    | ✅ Dinamis     |
| **Cart Add**        | `CartController::store()`      | `POST /customer/cart/add/{product}`                 | ✅             |
| **Cart Update**     | `CartController::update()`     | `PATCH /customer/cart/update`                       | ✅             |
| **Cart Remove**     | `CartController::destroy()`    | `DELETE /customer/cart/remove/{cartItem}`           | ✅             |
| **Wishlist Add**    | `WishlistController::store()`  | `POST /customer/wishlist/add/{product}`             | ✅             |
| **Wishlist Remove** | `WishlistController::remove()` | `DELETE /customer/wishlist/remove/{wishlistItem}`   | ✅             |
| **Coupon Apply**    | `CouponController::apply()`    | `POST /customer/coupon/apply`                       | ✅             |
| **Coupon Remove**   | `CouponController::remove()`   | `DELETE /customer/coupon/remove`                    | ✅             |
| **Review Submit**   | `ReviewController::store()`    | `POST /customer/reviews` → `customer.reviews.store` | ✅             |




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
- `OrderController@update` — update status + payment_status, stock restore on cancel, trigger `order_cancelled` notification
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
- **CartItem** `sub_total` **accessor bug** — `$this->product?->discount` → `$this->product?->has_discount` supaya diskon expired tidak terpakai ✅
- **Midtrans Snap gagal → order orphan** — generate Snap token SEBELUM `DB::transaction`, kalau gagal order tidak jadi dibuat ✅
- **Midtrans payment status separated** — `expire` dipisah dari `deny/cancel` supaya notifikasi lebih spesifik (`payment_expired` vs `payment_failed`) ✅



### Customer Review Submit

- **Controller:** `Customer\ReviewController@store` — validasi rating 1-5 + comment, cek order item user (completed/delivered), simpan review, trigger `new_review` notification
- **Route:** `POST /customer/reviews` → `customer.reviews.store`
- **View:** review form di `product-detail.blade.php` — `@auth` kondisional, `@csrf`, hidden `product_id`, select rating, textarea comment



### Admin Notification System

- **Migration:** tambah kolom `type` (string) + `data` (JSON) ke tabel `notifications`
- **Model:** `Notification` — casts `data` sebagai array, scopes `unread()`, `forAdmin()`, `latest()`
- **Service:** `NotificationService::send($type, $title, $data)` — simpan notifikasi global (`user_id = null`)
- **Controller:** `Admin\NotificationController` — `markAsRead()` via AJAX, `readAll()` via POST
- **ViewComposer:** `AppServiceProvider` — inject `unreadNotifications` (latest 5) + `unreadCount` ke `admin-navbar`
- **Navbar Dropdown:** dinamis dengan looping, icon per tipe notifikasi (`item-1` s/d `item-4`), AJAX mark-as-read + badge count auto-decrement
- **Notification Types:**

  | Type                         | Trigger                                                | Lokasi                                      |
  | ---------------------------- | ------------------------------------------------------ | ------------------------------------------- |
  | `order_placed`               | Customer checkout sukses (COD/Midtrans)                | `CheckoutController@store`                  |
  | `payment_settlement`         | Webhook Midtrans settlement                            | `MidtransController@handleCallback`         |
  | `payment_failed`             | Webhook Midtrans deny/cancel                           | `MidtransController@handleCallback`         |
  | `payment_expired`            | Webhook Midtrans expire                                | `MidtransController@handleCallback`         |
  | `low_stock` / `out_of_stock` | Stock ≤ 10 / stock = 0 setelah checkout                | `CheckoutController@store`                  |
  | `low_stock` / `out_of_stock` | Stock ≤ 10 / stock = 0 saat admin create/update produk | `Admin\ProductController@store` / `@update` |
  | `new_review`                 | Customer submit review                                 | `Customer\ReviewController@store`           |
  | `order_cancelled`            | Admin cancel order                                     | `Admin\OrderController@update`              |

- **Cleanup Command:** `notifications:clean` — hapus notifikasi `is_read = true` > 7 hari, terjadwal `daily()` via `routes/console.php`



### Routes (web.php) — 88+ Named Routes

- **Public/Guest (14):** `/`, register, login, password reset, `/products`, `/products/search`, `/products/{slug}`, `/cart`, `/wishlist`
- **Auth (7):** email verification, password confirm, password update, logout
- **Admin (~38):** prefix `admin/`, middleware `auth+role:admin`:
  - 5 full resource: brands, categories, products, coupons, sliders
  - 1 resource (partial): orders
  - 3 read-only: customers, reviews, activities
  - 1 setting: index + update
  - 1 dashboard
  - 2 notifications: markAsRead, readAll
- **Customer auth (8):** prefix `customer/`, middleware `auth+role:customer`:
  - dashboard, cart CRUD (3), wishlist CRUD (2), coupon apply/remove (2)

---



## ⬜ Sebagian (Controller Parsial, View Terbatas)



### Admin Dashboard — Fully Dynamic

- **12 stat cards** (6+6) dengan data real dari database
  - Total Orders, Total Amount, Pending, Completed, Shipped, Canceled
  - Total Products, Total Customers (dari User role customer)
- **Chart ApexCharts** — 3 periode: This Year (monthly), This Week (daily), Last Week (daily)
  - Series: Completed + Canceled
  - Dropdown filter interaktif via jQuery
- **Revenue Growth** — % perubahan completed orders bulan ini vs bulan lalu
- **Order Growth** — % perubahan jumlah completed orders bulan ini vs bulan lalu
- **Best Selling Products** — Top 5 produk berdasarkan total quantity terjual
- **Recent Orders** — 10 order terbaru dengan badges status

### Customer Dashboard — Fully Dynamic

- **Controller:** `Customer\DashboardController@index`
- **3 stat cards:** Total Orders, Total Spending, Wishlist
- **Recent Orders** — 5 order terbaru dengan link ke detail
- **Notifications** — 5 notifikasi terbaru
- **Styling:** Bootstrap card border rounded-3 shadow-sm (konsisten)


### Customer Orders — Redesigned UI

- **Orders Index** — card wrapper, `table-hover`, kolom sederhana, badges konsisten
- **Orders Show** — semua `wg-box` diganti Bootstrap `card`, tabel pakai `table-hover`
- 4 section seragam: Order Details, Ordered Items, Shipping Address, Transactions


### Admin — Controller hanya index saja (atau parsial)


| Fitur      | Controller             | View              | Route      | ActivityLog   |
| ---------- | ---------------------- | ----------------- | ---------- | ------------- |
| Customers  | `index()` saja         | `index.blade.php` | GET only   | ❌             |
| Reviews    | `index()` saja         | `index.blade.php` | GET only   | ❌             |
| Activities | `index()` saja         | `index.blade.php` | GET only   | ❌ (read-only) |
| Settings   | `index()` + `update()` | `index.blade.php` | GET + POST | ❌             |




### Customer Area — View Saja (Controller Belum / Masih Perlu)


| Fitur     | View Files                             | Controller  | Routes      |
| --------- | -------------------------------------- | ----------- | ----------- |
| Dashboard | `customer/dashboard/index.blade.php`   | ❌ (Closure) | Static view |
| Account   | `customer/dashboard/account.blade.php` | ❌           | ❌           |
| History   | `customer/dashboard/history.blade.php` | ❌           | ❌           |


---



## ❌ Belum Tersentuh Sama Sekali


| Fitur                         | Prioritas | Keterangan                                     |
| ----------------------------- | --------- | ---------------------------------------------- |
| Discount Scheduler            | 🟡 Sedang | Butuh Laravel Task Scheduler + console command |
| PDF Invoice                   | 🟡 Sedang | Butuh `barryvdh/laravel-dompdf`                |
| Auto Rating Calculation       | 🟢 Ringan | -                                              |
| Soft Delete Product           | 🟢 Ringan | -                                              |
| Activity Log Automation       | 🟢 Ringan | Masih manual di controller                     |


---



## 🔧 Issues / Technical Debt


| Issue                                                        | Detail                                                     | Severity  |
| ------------------------------------------------------------ | ---------------------------------------------------------- | --------- |
| Register tidak assign role                                   | User baru daftar dapat `role_id = null`                    | 🔴 Tinggi |
| Route profile di-comment                                     | `routes/web.php` — profile edit/update/destroy tidak aktif | 🟡 Sedang |
| Coupon tidak punya `max_uses` / `usage_limit`                | Bisa dipakai unlimited tanpa batas                         | 🟡 Sedang |
| Tidak ada email/notifikasi setelah order                     | Customer tidak dapat konfirmasi order lewat email          | 🟡 Sedang |
| Activity log manual                                          | Belum pakai event/listener atau observer                   | 🟡 Sedang |
| Admin Brand/Category/Product `show()`                        | Method `show()` ada tapi isinya kosong (stub)              | 🟢 Ringan |
| `onPending` Midtrans redirect ke `#`                         | Harusnya redirect ke order show page                       | 🟢 Ringan |
| `shipping_cost` hardcoded 0                                  | Tidak ada shipping API integration                         | 🟢 Ringan |
| VAT hardcoded 1000                                           | Harusnya configurable / percentage-based                   | 🟢 Ringan |
| Address tidak punya `type` (Rumah/Kantor/Kos) & `is_default` | Disebut di BRIEF                                           | 🟢 Ringan |
| Discount row duplicate `id="cart-vat"`                       | ID sama dengan VAT row, tidak ada JS yang pakai            | 🟢 Ringan |
| Customer `history.blade.php` & `account.blade.php`           | Kedua view masih kosong (0 baris)                          | 🟢 Ringan |


---



## Rekomendasi Prioritas Pengembangan


| Urutan | Fitur                                      | Alasan                                                 |
| ------ | ------------------------------------------ | ------------------------------------------------------ |
| 1      | **Assign role saat register**              | User baru dapat `role_id = null`, perlu di-fix         |
| 2      | **Discount Scheduler**                     | Otomatisasi diskon (start/end, auto return price)      |
| 3      | **Coupon max_uses**                        | Limitasi pemakaian kupon                               |
| 4      | **PDF Invoice**                            | Download invoice setelah payment                       |
| 5      | **Midtrans** `onPending` **redirect**      | Fix broken redirect                                    |
| 6      | **Activity Log Automation**                | Event/listener ganti manual                            |
| 7      | **Customer empty views**                   | `history.blade.php` & `account.blade.php` masih kosong |
| 8      | **Shipping cost integration**              | API shipping / ongkir                                  |
| 9      | **Soft Delete Product**                    | Data safety                                            |
| 10     | **Auto Rating Calculation**                | Hitung rating otomatis dari reviews                    |


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
- [x] Activity Log (Login, Product, Order, Coupon)



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
- [x] Product Review (Rating 1-5, Submit form functional)
- [x] Notification (Admin: Order, Payment, Stock, Review, Cancel)
- [ ] Invoice (Download PDF)

---



## Stack & Dependencies



### Terpasang


| Package                 | Version |
| ----------------------- | ------- |
| `laravel/framework`     | ^13.8   |
| `laravel/breeze`        | ^2.4    |
| `pestphp/pest`          | ^4.7    |
| `laravel/pint`          | ^1.27   |
| `laravel/debugbar`      | ^4.4    |
| `midtrans/midtrans-php` | ^2.6.2  |




### Perlu Ditambahkan


| Package                                 | Untuk                 |
| --------------------------------------- | --------------------- |
| `barryvdh/laravel-dompdf`               | PDF invoice           |
| `spatie/laravel-activitylog` (opsional) | Activity log otomatis |


---



## Total File Summary

```
app/Models/              → 20 models (5 tanpa mass assignment protection — Address, CouponUsage, Order, OrderItem, Role)
app/Services/            → 1 service (NotificationService)
app/Console/Commands/    → 1 command (CleanOldNotifications)
app/Http/Controllers/    → 32 controllers (11 admin: 4 partial; 11 customer: all complete; 9 auth; 1 base)
resources/views/         → 80 Blade files
database/migrations/     → 24 migrations
database/seeders/        → 1 seeder (DatabaseSeeder)
database/factories/      → 6 factories (+ ReviewFactory)
routes/                  → web.php, auth.php, console.php
tests/                   → 11 test files (semua Breeze default)
planning/                → 6 dokumen (BRIEF, ERD, USE_CASE, SEQUENCE, ACTIVITY, progres)
```

