@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Cart</h2>
      <div class="checkout-steps">
        <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Shopping Bag</span>
            <em>Manage Your Items List</em>
          </span>
        </a>
        <a href="#" class="checkout-steps__item">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Shipping and Checkout</span>
            <em>Checkout Your Items List</em>
          </span>
        </a>
        <a href="#" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Confirmation</span>
            <em>Review And Submit Your Order</em>
          </span>
        </a>
      </div>

      @if ($cartItems->isEmpty())
        <div class="text-center py-5">
          <h3>Your cart is empty</h3>
          <a href="{{ route('products.index') }}" class="btn btn-primary btn-checkout mt-3">SHOP NOW</a>
        </div>
      @else
        <div class="shopping-cart">
          <div class="cart-table__wrapper">
            <form method="POST" action="{{ route('customer.cart.update') }}" id="cart-form">
              @csrf
              @method('PATCH')
              <table class="cart-table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th></th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($cartItems as $item)
                    @php
                      $product = $item->product;

                      $price = $product?->price ?? 0;
                      $itemSubtotal = $price * $item->quantity;
                    @endphp
                    <tr>
                      <td>
                        <div class="shopping-cart__product-item">
                          <img loading="lazy" src="{{ $item->product?->first_image }}" width="120" height="120"
                            alt="{{ $product?->name ?? 'Product' }}" />
                        </div>
                      </td>
                      <td>
                        <div class="shopping-cart__product-item__detail">
                          <h4>{{ $product?->name ?? 'Unknown Product' }}</h4>
                        </div>
                      </td>
                      <td>
                        @if ($product?->has_discount)
                          <span
                            class="shopping-cart__product-price price-old">Rp{{ number_format($price, 0, ',', '.') }}</span>
                          <span
                            class="shopping-cart__product-price">Rp{{ number_format($product->discount->value, 0, ',', '.') }}
                          </span>
                        @else
                          <span class="shopping-cart__product-price">Rp{{ number_format($price, 0, ',', '.') }}</span>
                        @endif
                      </td>
                      <td>
                        <div class="qty-control position-relative">
                          <input type="number" name="quantities[{{ $item->id }}]" value="{{ $item->quantity }}"
                            min="1" class="qty-control__number text-center">
                          <div class="qty-control__reduce">-</div>
                          <div class="qty-control__increase">+</div>
                        </div>
                      </td>
                      <td>
                        <span class="shopping-cart__subtotal">Rp{{ number_format($item->sub_total, 0, ',', '.') }}</span>
                      </td>
                      <td>
                        <a href="#" class="remove-cart"
                          onclick="event.preventDefault(); document.getElementById('remove-form-{{ $item->id }}').submit();">
                          <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                            <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                          </svg>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </form>
            <div class="cart-table-footer">
              @if ($coupon)
                <form action="{{ route('customer.coupon.remove') }}" method="POST" class="position-relative bg-body"
                  id="coupon-form">
                  @csrf
                  @method('delete')
                  <input class="form-control" type="text" name="coupon_code" placeholder="Coupon Code"
                    value="{{ $coupon->code }}">
                  <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit"
                    value="REMOVE COUPON">
                </form>
              @else
                <form action="{{ route('customer.coupon.apply') }}" method="POST" class="position-relative bg-body"
                  id="coupon-form">
                  @csrf
                  <input class="form-control" type="text" name="coupon_code" placeholder="Coupon Code">
                  <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit"
                    value="APPLY COUPON">
                </form>
              @endif
              <div id="coupon-message" class="mt-2 small"></div>
              <button type="submit" form="cart-form" class="btn btn-light">UPDATE CART</button>
            </div>
            @if (session('success'))
              <div class="alert alert-success mt-2 mb-0">
                {{ session('success') }}
              </div>
            @endif
            @if (session('error'))
              <div class="alert alert-danger mt-2 mb-0">
                {{ session('error') }}
              </div>
            @endif
          </div>
          @foreach ($cartItems as $item)
            <form action="{{ route('customer.cart.remove', $item) }}" method="POST"
              id="remove-form-{{ $item->id }}" class="d-none">
              @csrf
              @method('DELETE')
            </form>
          @endforeach
          <div class="shopping-cart__totals-wrapper">
            <div class="sticky-content">
              <div class="shopping-cart__totals">
                <h3>Cart Totals</h3>
                <table class="cart-totals" id="cart-totals" data-subtotal="{{ $subtotal }}"
                  data-vat="{{ $vat }}">
                  <tbody>
                    <tr>
                      <th>Subtotal</th>
                      <td id="cart-subtotal" class="text-end">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if ($vat)
                      <tr>
                        <th>VAT</th>
                        <td id="cart-vat" class="text-end">Rp{{ number_format($vat, 0, ',', '.') }}</td>
                      </tr>
                    @endif
                    @if ($discount)
                      <tr>
                        <th>Dicount</th>
                        <td id="cart-vat" class="text-end">-Rp{{ number_format($discount, 0, ',', '.') }}</td>
                      </tr>
                    @endif
                    <tr>
                      <th>Total</th>
                      <td id="cart-total" class="text-end">Rp{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="mobile_fixed-btn_wrapper">
                <div class="button-wrapper container">
                  <a href="{{ route('customer.checkout.index') }}" class="btn btn-primary btn-checkout">
                    PROCEED TO CHECKOUT
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif
    </section>
  </main>
@endsection
