@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-md-1 pb-md-3"></div>
    <section class="product-single container">
      <div class="row">
        <div class="col-lg-7">
          <div class="product-single__media" data-media-type="vertical-thumbnail">
            <div class="product-single__image">
              <div class="swiper-container">
                <div class="swiper-wrapper">
                  @forelse ($product->images as $image)
                    @php
                      $imgUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($image->image)
                          ? asset('storage/' . $image->image)
                          : asset('image-600x400.png');
                    @endphp
                    <div class="swiper-slide product-single__image-item">
                      <img loading="lazy" class="h-auto" src="{{ $imgUrl }}" width="674" height="674"
                        alt="{{ $product->name }}" />
                      <a data-fancybox="gallery" href="{{ $imgUrl }}" data-bs-toggle="tooltip"
                        data-bs-placement="left" title="Zoom">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <use href="#icon_zoom" />
                        </svg>
                      </a>
                    </div>
                  @empty
                    <div class="swiper-slide product-single__image-item">
                      <img loading="lazy" class="h-auto" src="{{ asset('image-600x400.png') }}" width="674"
                        height="674" alt="{{ $product->name }}" />
                    </div>
                  @endforelse
                </div>
                <div class="swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                    xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_prev_sm" />
                  </svg></div>
                <div class="swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11"
                    xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_next_sm" />
                  </svg></div>
              </div>
            </div>
            <div class="product-single__thumbnail">
              <div class="swiper-container">
                <div class="swiper-wrapper">
                  @forelse ($product->images as $image)
                    @php
                      $thumbUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($image->image)
                          ? asset('storage/' . $image->image)
                          : asset('image-600x400.png');
                    @endphp
                    <div class="swiper-slide product-single__image-item">
                      <img loading="lazy" class="h-auto" src="{{ $thumbUrl }}" width="104" height="104"
                        alt="{{ $product->name }}" />
                    </div>
                  @empty
                    <div class="swiper-slide product-single__image-item">
                      <img loading="lazy" class="h-auto" src="{{ asset('image-600x400.png') }}" width="104"
                        height="104" alt="{{ $product->name }}" />
                    </div>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="d-flex justify-content-between mb-4 pb-md-2">
            <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
              <a href="{{ route('home') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
              <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
              <a href="{{ route('products.index') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">The
                Shop</a>
              @if ($product->category)
                <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                  class="menu-link menu-link_us-s text-uppercase fw-medium">{{ $product->category->name }}</a>
              @endif
            </div>
            <div
              class="product-single__prev-next d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">
              <a href="#" class="text-uppercase fw-medium"><svg width="10" height="10" viewBox="0 0 25 25"
                  xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_prev_md" />
                </svg><span class="menu-link menu-link_us-s">Prev</span></a>
              <a href="#" class="text-uppercase fw-medium"><span class="menu-link menu-link_us-s">Next</span><svg
                  width="10" height="10" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_next_md" />
                </svg></a>
            </div>
          </div>
          <h1 class="product-single__name">{{ $product->name }}</h1>
          <div class="product-single__rating">
            <div class="reviews-group d-flex">
              @for ($i = 1; $i <= 5; $i++)
                <svg class="review-star{{ $i <= round($product->reviews_avg_rating ?? 0) ? ' review-star_active' : '' }}"
                  viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_star" />
                </svg>
              @endfor
            </div>
            <span class="reviews-note text-lowercase text-secondary ms-1">{{ $product->reviews->count() }}
              review(s)
            </span>
          </div>
          <div class="product-single__price">
            @if ($product->has_discount)
              <span class="current-price">
                <span class="money price price-old">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                <span
                  class="money price price-sale">Rp{{ number_format($product->discount->value, 0, ',', '.') }}</span>
              </span>
            @else
              <span class="current-price"><span
                  class="money price">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
              </span>
            @endif
          </div>
          <div class="product-single__short-desc">
            <p>{{ $product->description }}</p>
          </div>
          @if ($cartItem)
            <form method="POST" action="{{ route('customer.cart.remove', $cartItem) }}">
              @csrf @method('DELETE')
              <div class="product-single__addtocart">
                <button type="submit" class="btn btn-danger btn-addtocart w-100">Remove from Cart</button>
              </div>
            </form>
          @else
            <form name="addtocart-form" method="post" action="{{ route('customer.cart.add', $product) }}">
              @csrf
              <div class="product-single__addtocart">
                <div class="qty-control position-relative">
                  <input type="number" name="quantity" value="1" min="1"
                    class="qty-control__number text-center">
                  <div class="qty-control__reduce">-</div>
                  <div class="qty-control__increase">+</div>
                </div>
                <button type="submit" class="btn btn-primary btn-addtocart">Add to Cart</button>
              </div>
            </form>
          @endif
          <div class="product-single__addtolinks">
            @if ($wishlistItem)
              <a href="#" class="menu-link menu-link_us-s add-to-wishlist"
                onclick="event.preventDefault(); document.getElementById('remove-wishlist-form').submit();">
                <svg width="16" height="16" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="color: #dc2626">
                  <use href="#icon_heart_filled" />
                </svg><span>Remove from Wishlist</span>
              </a>
              <form method="POST" action="{{ route('customer.wishlist.remove', $wishlistItem) }}"
                id="remove-wishlist-form" class="d-none">
                @csrf @method('DELETE')
              </form>
            @else
              @auth
                <a href="#" class="menu-link menu-link_us-s add-to-wishlist"
                  onclick="event.preventDefault(); document.getElementById('add-wishlist-form').submit();">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_heart" />
                  </svg><span>Add to Wishlist</span>
                </a>
                <form method="POST" action="{{ route('customer.wishlist.add', $product) }}"
                  id="add-wishlist-form" class="d-none">
                  @csrf
                </form>
              @else
                <a href="{{ route('login') }}" class="menu-link menu-link_us-s add-to-wishlist">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_heart" />
                  </svg><span>Add to Wishlist</span>
                </a>
              @endauth
            @endif
            <share-button class="share-button">
              <button class="menu-link menu-link_us-s to-share border-0 bg-transparent d-flex align-items-center">
                <svg width="16" height="19" viewBox="0 0 16 19" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_sharing" />
                </svg>
                <span>Share</span>
              </button>
              <details id="Details-share-template__main" class="m-1 xl:m-1.5" hidden="">
                <summary class="btn-solid m-1 xl:m-1.5 pt-3.5 pb-3 px-5">+</summary>
                <div id="Article-share-template__main"
                  class="share-button__fallback flex items-center absolute top-full left-0 w-full px-2 py-4 bg-container shadow-theme border-t z-10">
                  <div class="field grow mr-4">
                    <label class="field__label sr-only" for="url">Link</label>
                    <input type="text" class="field__input w-full" id="url" value="{{ url()->current() }}"
                      placeholder="Link" onclick="this.select();" readonly="">
                  </div>
                  <button class="share-button__copy no-js-hidden">
                    <svg class="icon icon-clipboard inline-block mr-1" width="11" height="13" fill="none"
                      xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" viewBox="0 0 11 13">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M2 1a1 1 0 011-1h7a1 1 0 011 1v9a1 1 0 01-1 1V1H2zM1 2a1 1 0 00-1 1v9a1 1 0 001 1h7a1 1 0 001-1V3a1 1 0 00-1-1H1zm0 10V3h7v9H1z"
                        fill="currentColor"></path>
                    </svg>
                    <span class="sr-only">Copy link</span>
                  </button>
                </div>
              </details>
            </share-button>
          </div>
          <div class="product-single__meta-info">
            <div class="meta-item">
              <label>SKU:</label>
              <span>N/A</span>
            </div>
            <div class="meta-item">
              <label>Categories:</label>
              <span>
                @if ($product->category)
                  <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                    class="menu-link">{{ $product->category->name }}</a>
                @else
                  N/A
                @endif
              </span>
            </div>
            @if ($product->brand)
              <div class="meta-item">
                <label>Brand:</label>
                <span>{{ $product->brand->name }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>
      <div class="product-single__details-tab">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab"
              href="#tab-description" role="tab" aria-controls="tab-description"
              aria-selected="true">Description</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore" id="tab-additional-info-tab" data-bs-toggle="tab"
              href="#tab-additional-info" role="tab" aria-controls="tab-additional-info"
              aria-selected="false">Additional Information</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore" id="tab-reviews-tab" data-bs-toggle="tab" href="#tab-reviews"
              role="tab" aria-controls="tab-reviews" aria-selected="false">Reviews
              ({{ $product->reviews->count() }})</a>
          </li>
        </ul>
        <div class="tab-content">
          {{-- description --}}
          <div class="tab-pane fade show active" id="tab-description" role="tabpanel"
            aria-labelledby="tab-description-tab">
            <div class="product-single__description">
              <h3 class="block-title mb-4">{{ $product->name }}</h3>
              <p class="content">{{ $product->description }}</p>
            </div>
          </div>
          {{-- Information --}}
          <div class="tab-pane fade" id="tab-additional-info" role="tabpanel"
            aria-labelledby="tab-additional-info-tab">
            <div class="product-single__addtional-info">
              <div class="item">
                <label class="h6">Weight</label>
                <span>N/A</span>
              </div>
              <div class="item">
                <label class="h6">Dimensions</label>
                <span>N/A</span>
              </div>
              <div class="item">
                <label class="h6">Size</label>
                <span>XS, S, M, L, XL</span>
              </div>
              <div class="item">
                <label class="h6">Color</label>
                <span>Black, Orange, White</span>
              </div>
              @if ($product->brand)
                <div class="item">
                  <label class="h6">Brand</label>
                  <span>{{ $product->brand->name }}</span>
                </div>
              @endif
            </div>
          </div>
          {{-- reviews --}}
          <div class="tab-pane fade" id="tab-reviews" role="tabpanel" aria-labelledby="tab-reviews-tab">
            <h2 class="product-single__reviews-title">Reviews</h2>
            <div class="product-single__reviews-list">
              @forelse ($product->reviews as $review)
                <div class="product-single__reviews-item">
                  <div class="customer-avatar">
                    <img loading="lazy" src="{{ asset('assets/images/avatar.jpg') }}" alt="">
                  </div>
                  <div class="customer-review">
                    <div class="customer-name">
                      <h6>{{ $review->user->name ?? 'Anonymous' }}</h6>
                      <div class="reviews-group d-flex">
                        @for ($i = 1; $i <= 5; $i++)
                          <svg class="review-star{{ $i <= $review->rating ? ' review-star_active' : '' }}"
                            viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_star" />
                          </svg>
                        @endfor
                      </div>
                    </div>
                    <div class="review-date">{{ $review->created_at->format('F d, Y') }}</div>
                    <div class="review-text">
                      <p>{{ $review->comment }}</p>
                    </div>
                  </div>
                </div>
              @empty
                <p class="text-secondary">No reviews yet.</p>
              @endforelse
            </div>
            @auth
              <div class="product-single__review-form">
                <form action="{{ route('customer.reviews.store') }}" method="POST">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $product->id }}">
                  <h5>Write a review for “{{ $product->name }}”</h5>
                  <div class="select-star-rating">
                    <label>Your rating *</label>
                    <select name="rating" class="form-control form-control_gray" required>
                      <option value="">Select rating</option>
                      <option value="5">5 - Excellent</option>
                      <option value="4">4 - Good</option>
                      <option value="3">3 - Average</option>
                      <option value="2">2 - Poor</option>
                      <option value="1">1 - Terrible</option>
                    </select>
                  </div>
                  <div class="mb-4">
                    <textarea name="comment" class="form-control form-control_gray" placeholder="Your Review" cols="30" rows="8" required></textarea>
                  </div>
                  <div class="form-action">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
              </div>
            @else
              <div class="product-single__review-form">
                <p class="text-secondary"><a href="{{ route('login') }}">Login</a> to write a review.</p>
              </div>
            @endauth
          </div>
        </div>
      </div>
    </section>
    <section class="products-carousel container">
      <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Related <strong>Products</strong></h2>
      <div id="related_products" class="position-relative">
        <div class="swiper-container js-swiper-slider"
          data-settings='{
            "autoplay": false,
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": false,
            "pagination": {
              "el": "#related_products .products-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": "#related_products .products-carousel__next",
              "prevEl": "#related_products .products-carousel__prev"
            },
            "breakpoints": {
              "320": { "slidesPerView": 2, "slidesPerGroup": 2, "spaceBetween": 14 },
              "768": { "slidesPerView": 3, "slidesPerGroup": 3, "spaceBetween": 24 },
              "992": { "slidesPerView": 4, "slidesPerGroup": 4, "spaceBetween": 30 }
            }
          }'>
          <div class="swiper-wrapper">
            @foreach ($related as $item)
              <div class="swiper-slide product-card">
                <div class="pc__img-wrapper">
                  <a href="{{ route('products.show', $item->slug) }}">
                    @php
                      $firstImg = $item->images->first();
                      $img1 =
                          $firstImg && \Illuminate\Support\Facades\Storage::disk('public')->exists($firstImg->image)
                              ? asset('storage/' . $firstImg->image)
                              : asset('image-600x400.png');
                      $secondImg = $item->images->skip(1)->first();
                      $img2 =
                          $secondImg && \Illuminate\Support\Facades\Storage::disk('public')->exists($secondImg->image)
                              ? asset('storage/' . $secondImg->image)
                              : $img1;
                    @endphp
                    <img loading="lazy" src="{{ $img1 }}" width="330" height="400"
                      alt="{{ $item->name }}" class="pc__img">
                    <img loading="lazy" src="{{ $img2 }}" width="330" height="400"
                      alt="{{ $item->name }}" class="pc__img pc__img-second">
                  </a>
                </div>
                <div class="pc__info position-relative">
                  <p class="pc__category">{{ $item->category->name ?? '' }}</p>
                  <h6 class="pc__title"><a href="{{ route('products.show', $item->slug) }}">{{ $item->name }}</a>
                  </h6>
                  <div class="product-card__price d-flex">
                    @php
                      $hasDisc =
                          $item->discount &&
                          (!$item->discount->start_date || now() >= $item->discount->start_date) &&
                          (!$item->discount->end_date || now() <= $item->discount->end_date);
                    @endphp
                    @if ($hasDisc)
                      <span class="money price price-old">Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                      <span class="money price price-sale">Rp{{ number_format($item->discount->value, 0, ',', '.') }}
                      </span>
                    @else
                      <span class="money price">{{ number_format($item->price, 0, ',', '.') }}</span>
                    @endif
                  </div>
                  @if ($item->reviews_avg_rating)
                    <div class="product-card__review d-flex align-items-center">
                      <div class="reviews-group d-flex">
                        @for ($i = 1; $i <= 5; $i++)
                          <svg
                            class="review-star{{ $i <= round($item->reviews_avg_rating) ? ' review-star_active' : '' }}"
                            viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_star" />
                          </svg>
                        @endfor
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
        <div class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
          <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_prev_md" />
          </svg>
        </div>
        <div class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
          <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_next_md" />
          </svg>
        </div>
        <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
      </div>
    </section>
  </main>
@endsection
