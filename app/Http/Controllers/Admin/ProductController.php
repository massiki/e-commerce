<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with([
            'category',
            'brand',
            'images',
            'discount' => function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('start_date')
                        ->orWhereDate('start_date', '<=', now());
                })->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', now());
                });
            },
        ])->when($request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('category', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('brand', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        })->latest()->paginate(10)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'description' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'stock' => 'required|integer|min:0',
            'featured' => 'boolean',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter++;
        }

        $product = DB::transaction(function () use ($request, $slug) {
            $product = Product::create([
                'name' => $request->name,
                'slug' => $slug,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'featured' => $request->boolean('featured'),
            ]);

            foreach ($request->file('images', []) as $image) {
                $imagePath = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imagePath,
                ]);
            }

            if ($request->sale_price || $request->discount_start || $request->discount_end) {
                Discount::create([
                    'product_id' => $product->id,
                    'value' => $request->sale_price ?? 0,
                    'start_date' => $request->discount_start,
                    'end_date' => $request->discount_end,
                ]);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => "Created product: {$product->name}",
            ]);

            return $product;
        });

        $this->checkStockNotification($product);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'stock' => 'required|integer|min:0',
            'featured' => 'boolean',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
            $slug = $originalSlug.'-'.$counter++;
        }

        $deletedImageIds = collect(explode(',', $request->deleted_images))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->toArray();

        DB::transaction(function () use ($request, $product, $slug, $deletedImageIds) {
            $product->update([
                'name' => $request->name,
                'slug' => $slug,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'featured' => $request->boolean('featured'),
            ]);

            if (! empty($deletedImageIds)) {
                $imagesToDelete = ProductImage::whereIn('id', $deletedImageIds)
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    if (Storage::disk('public')->exists($image->image)) {
                        Storage::disk('public')->delete($image->image);
                    }
                    $image->delete();
                }
            }

            foreach ($request->file('images', []) as $image) {
                $imagePath = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imagePath,
                ]);
            }

            if ($request->sale_price || $request->discount_start || $request->discount_end) {
                $discountData = [
                    'value' => $request->sale_price ?? 0,
                    'start_date' => $request->discount_start,
                    'end_date' => $request->discount_end,
                ];

                if ($product->discount) {
                    $product->discount->update($discountData);
                } else {
                    $product->discount()->create($discountData);
                }
            } elseif ($product->discount) {
                $product->discount->delete();
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => "Updated product: {$product->name}",
            ]);
        });

        $this->checkStockNotification($product);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;

        DB::transaction(function () use ($product) {
            if ($product->discount) {
                $product->discount->delete();
            }

            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                }
                $image->delete();
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => "Deleted product: {$product->name}",
            ]);

            $product->delete();
        });

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    private function checkStockNotification(Product $product): void
    {
        if ($product->stock === 0) {
            NotificationService::send('out_of_stock', "{$product->name} is out of stock", [
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);
        } elseif ($product->stock <= 10) {
            NotificationService::send('low_stock', "{$product->name} stock is low ({$product->stock} left)", [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'stock' => $product->stock,
            ]);
        }
    }
}
