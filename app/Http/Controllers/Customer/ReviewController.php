<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Services\LogActivityService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $orderItem = OrderItem::whereHas('order', function ($q) {
            $q->where('user_id', Auth::id())->whereIn('status', ['completed', 'delivered']);
        })
            ->where('product_id', $product->id)
            ->whereDoesntHave('review')
            ->first();

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'order_item_id' => $orderItem?->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        LogActivityService::log("Created review for {$product->name} ({$review->rating}/5)");

        NotificationService::send('new_review', "New review for {$product->name} ({$review->rating}/5)", [
            'review_id' => $review->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'rating' => $review->rating,
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully.');
    }
}
