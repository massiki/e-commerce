<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('components.customer-navbar', function ($view) {
            $cartCount = auth()->check()
                ? Cart::firstOrCreate(['user_id' => auth()->id()])->items()->count()
                : 0;

            $view->with('cartCount', $cartCount);
        });

        View::composer('components.admin-navbar', function ($view) {
            $unreadNotifications = Notification::forAdmin()->unread()->latest()->take(5)->get();
            $unreadCount = Notification::forAdmin()->unread()->count();

            $view->with('unreadNotifications', $unreadNotifications);
            $view->with('unreadCount', $unreadCount);
        });

        // aktifkan juka menggunakan ngrok
        // if (config('app.env') === 'local') {
        //     URL::forceScheme('https');
        // }
    }
}
