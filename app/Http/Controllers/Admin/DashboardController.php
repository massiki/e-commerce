<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalAmount = Order::sum('total');

        $pendingOrders = Order::where('status', 'pending')->count();
        $pendingAmount = Order::where('status', 'pending')->sum('total');

        $completedOrders = Order::where('status', 'completed')->count();
        $completedAmount = Order::where('status', 'completed')->sum('total');

        $canceledOrders = Order::where('status', 'cancelled')->count();
        $canceledAmount = Order::where('status', 'cancelled')->sum('total');

        $shippedOrders = Order::where('status', 'shipped')->count();
        $shippedAmount = Order::where('status', 'shipped')->sum('total');

        $totalProducts = Product::count();
        $totalCustomers = User::whereHas('role', fn ($q) => $q->where('name', 'customer'))->count();

        $monthlyOrders = Order::whereYear('created_at', now()->year)
            ->get()
            ->groupBy(fn ($o) => $o->created_at->format('n'))
            ->map(fn ($orders) => [
                'total' => $orders->sum('total'),
                'pending' => $orders->where('status', 'pending')->sum('total'),
                'shipped' => $orders->where('status', 'shipped')->sum('total'),
                'completed' => $orders->where('status', 'completed')->sum('total'),
                'cancelled' => $orders->where('status', 'cancelled')->sum('total'),
            ]);

        $currentRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)
            ->sum('total');

        $previousRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total');

        $revenueGrowth = $previousRevenue > 0
            ? round(($currentRevenue - $previousRevenue) / $previousRevenue * 100, 2)
            : 0;

        $currentOrderCount = Order::where('status', 'completed')
            ->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)
            ->count();

        $previousOrderCount = Order::where('status', 'completed')
            ->whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $orderGrowth = $previousOrderCount > 0
            ? round(($currentOrderCount - $previousOrderCount) / $previousOrderCount * 100, 2)
            : 0;

        $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        $thisWeekOrders = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get()
            ->groupBy(fn ($o) => $o->created_at->format('N'));

        $lastWeekOrders = Order::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->get()
            ->groupBy(fn ($o) => $o->created_at->format('N'));

        $buildWeeklySeries = function ($orders) {
            $completed = [];
            $canceled = [];
            foreach (range(1, 7) as $day) {
                $dayOrders = $orders->get((string) $day, collect());
                $completed[] = $dayOrders->where('status', 'completed')->sum('total');
                $canceled[] = $dayOrders->where('status', 'cancelled')->sum('total');
            }

            return [
                ['name' => 'Completed', 'data' => $completed],
                ['name' => 'Canceled', 'data' => $canceled],
            ];
        };

        $chartData = [
            'thisYear' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'series' => [
                    ['name' => 'Completed', 'data' => collect(range(1, 12))->map(fn ($m) => $monthlyOrders[$m]['completed'] ?? 0)->toArray()],
                    ['name' => 'Canceled', 'data' => collect(range(1, 12))->map(fn ($m) => $monthlyOrders[$m]['cancelled'] ?? 0)->toArray()],
                ],
            ],
            'thisWeek' => [
                'categories' => $dayNames,
                'series' => $buildWeeklySeries($thisWeekOrders),
            ],
            'lastWeek' => [
                'categories' => $dayNames,
                'series' => $buildWeeklySeries($lastWeekOrders),
            ],
        ];

        $bestSellingProducts = OrderItem::select('product_name', 'image', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->groupBy('product_name', 'image')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $recentOrders = Order::with('items')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalAmount',
            'pendingOrders',
            'pendingAmount',
            'completedOrders',
            'completedAmount',
            'canceledOrders',
            'canceledAmount',
            'shippedOrders',
            'shippedAmount',
            'totalProducts',
            'totalCustomers',
            'monthlyOrders',
            'revenueGrowth',
            'orderGrowth',
            'bestSellingProducts',
            'recentOrders',
            'currentRevenue',
            'currentOrderCount',
            'chartData'
        ));
    }
}
