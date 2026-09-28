<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Services\LogActivityService;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::firstOrCreate(
            ['user_id' => Auth::id()]
        );

        $wishlistItems = $wishlist->items()->with('product')->get();

        return view('customer.wishlists', compact('wishlistItems'));
    }

    public function store(Product $product)
    {
        $wishlist = Wishlist::firstOrCreate(
            ['user_id' => Auth::id()]
        );

        $exists = $wishlist->items()->where('product_id', $product->id)->exists();

        if (! $exists) {
            $wishlist->items()->create(['product_id' => $product->id]);
        }

        LogActivityService::log("Added product {$product->name} to wishlist");

        return back()->with('success', 'Product added to wishlist.');
    }

    public function remove(WishlistItem $wishlistItem)
    {
        abort_if($wishlistItem->wishlist?->user_id !== Auth::id(), 403);

        LogActivityService::log("Removed product {$wishlistItem->product->name} from wishlist");

        $wishlistItem->delete();

        return back()->with('success', 'Product removed from wishlist.');
    }
}
