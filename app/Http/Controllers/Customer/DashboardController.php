<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalOrders = $user->orders()->count();
        $totalSpending = $user->orders()->sum('total');
        $wishlistCount = $user->wishlist?->items()->count() ?? 0;
        $recentOrders = $user->orders()->with('items')->latest()->take(5)->get();
        $notifications = $user->notifications()->latest()->take(5)->get();

        return view('customer.dashboard.index', compact(
            'totalOrders',
            'totalSpending',
            'wishlistCount',
            'recentOrders',
            'notifications'
        ));
    }
}
