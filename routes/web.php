<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\CouponController as CustomerCouponController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\MidtransController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Customer\SearchController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [CustomerProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [SearchController::class, 'search'])->name('products.search');
Route::get('/products/{product:slug}', [CustomerProductController::class, 'show'])->name('products.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

// admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // brands
    Route::resource('brands', BrandController::class)->except(['show'])->names('brands');

    // categories
    Route::resource('categories', CategoryController::class)->except(['show'])->names('categories');

    // products
    Route::resource('products', ProductController::class)->except(['show'])->names('products');

    // coupons
    Route::resource('coupons', CouponController::class)->except(['show'])->names('coupons');

    // user customers
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');

    // reviews
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');

    // activities
    Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');

    // orders
    Route::resource('orders', OrderController::class)->names('orders');

    // sliders
    Route::resource('sliders', SliderController::class)->except(['show'])->names('sliders');

    // settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    // notifications
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
});

// customer
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // carts
    Route::post('/cart/add/{product}', [CartController::class, 'store'])->name('cart.add');
    Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'destroy'])->name('cart.remove');

    // wishlists
    Route::post('/wishlist/add/{product}', [WishlistController::class, 'store'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{wishlistItem}', [WishlistController::class, 'remove'])->name('wishlist.remove');

    // coupons
    Route::post('/coupon/apply', [CustomerCouponController::class, 'apply'])->name('coupon.apply');
    Route::delete('/coupon/remove', [CustomerCouponController::class, 'remove'])->name('coupon.remove');

    // addresses
    Route::resource('addresses', AddressController::class)->except(['show'])->names('addresses');

    // checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order:invoice_number}/confirmation', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    // reviews
    Route::post('/reviews', [CustomerReviewController::class, 'store'])->name('reviews.store');

    // orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order:invoice_number}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order:invoice_number}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');

});

// midtrans callback — no auth (external POST from Midtrans)
Route::post('/customer/midtrans/callback', [MidtransController::class, 'handleCallback'])->name('customer.handleCallback');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
