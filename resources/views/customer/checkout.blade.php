@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Shipping and Checkout</h2>
      <div class="checkout-steps">
        <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Shopping Bag</span>
            <em>Manage Your Items List</em>
          </span>
        </a>
        <a href="{{ route('customer.checkout.index') }}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Shipping and Checkout</span>
            <em>Checkout Your Items List</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Confirmation</span>
            <em>Review And Submit Your Order</em>
          </span>
        </a>
      </div>
      <form name="checkout-form" method="POST" action="{{ route('customer.checkout.store') }}">
        @csrf
        <input type="hidden" name="checkout_token" value="{{ session('checkout_token') }}">
        <div class="checkout-form">
          <div class="billing-info__wrapper">
            <div class="row">
              <div class="col-6">
                <h4>SHIPPING DETAILS</h4>
              </div>
              <div class="col-6">
              </div>
            </div>
            <div class="row mt-5">
              @if ($addresses->isNotEmpty())
                <div class="col-12 mb-4">
                  <label class="form-label fw-medium">Select Saved Address</label>
                  <select id="address-select" class="form-select">
                    <option value="">— Choose Address —</option>
                    @foreach ($addresses as $addr)
                      <option value="{{ $addr->id }}" data-recipient="{{ $addr->recipient_name }}"
                        data-phone="{{ $addr->phone }}" data-postal="{{ $addr->postal_code }}"
                        data-province="{{ $addr->province }}" data-city="{{ $addr->city }}"
                        data-district="{{ $addr->district }}" data-full="{{ $addr->full_address }}">
                        {{ $addr->recipient_name }} — {{ $addr->city }}, {{ $addr->province }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <input type="hidden" name="address_id" id="address_id" value="">
                <div class="col-md-6">
                  <div class="form-floating my-3">
                    <input type="text" class="form-control @error('recipient_name') is-invalid @enderror"
                      name="recipient_name" id="recipient_name" value="{{ old('recipient_name') }}" disabled>
                    <label for="recipient_name">Full Name *</label>
                    @error('recipient_name')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating my-3">
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                      id="phone" value="{{ old('phone') }}" disabled>
                    <label for="phone">Phone Number *</label>
                    @error('phone')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating my-3">
                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                      name="postal_code" id="postal_code" value="{{ old('postal_code') }}" disabled>
                    <label for="postal_code">Postal Code *</label>
                    @error('postal_code')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating mt-3 mb-3">
                    <input type="text" class="form-control @error('province') is-invalid @enderror" name="province"
                      id="province" value="{{ old('province') }}" disabled>
                    <label for="province">Province *</label>
                    @error('province')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating my-3">
                    <input type="text" class="form-control @error('city') is-invalid @enderror" name="city"
                      id="city" value="{{ old('city') }}" disabled>
                    <label for="city">City *</label>
                    @error('city')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-floating my-3">
                    <input type="text" class="form-control @error('district') is-invalid @enderror" name="district"
                      id="district" value="{{ old('district') }}" disabled>
                    <label for="district">District *</label>
                    @error('district')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-floating my-3">
                    <textarea class="form-control @error('full_address') is-invalid @enderror" name="full_address" id="full_address"
                      style="min-height:100px" disabled>{{ old('full_address') }}</textarea>
                    <label for="full_address">Full Address *</label>
                    @error('full_address')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              @else
                <div class="col-12">
                  <div class="alert alert-info d-flex align-items-center gap-3">
                    <span>You don't have any saved addresses yet. Please add one first.</span>
                    <a href="{{ route('customer.addresses.create') }}" class="btn btn-primary ms-auto">Add Address</a>
                  </div>
                </div>
              @endif
            </div>
          </div>
          <div class="checkout__totals-wrapper">
            <div class="sticky-content">
              <div class="checkout__totals">
                <h3>Your Order</h3>
                <table class="checkout-cart-items">
                  <thead>
                    <tr>
                      <th>PRODUCT</th>
                      <th align="right">SUBTOTAL</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($cartItems as $item)
                      <tr>
                        <td>
                          {{ $item->product?->name ?? 'Unknown Product' }} x {{ $item->quantity }}
                        </td>
                        <td align="right">
                          Rp{{ number_format($item->sub_total, 0, ',', '.') }}
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="2" class="text-center py-3">No items in cart</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
                <table class="checkout-totals">
                  <tbody>
                    <tr>
                      <th>SUBTOTAL</th>
                      <td align="right">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                      <th>SHIPPING</th>
                      <td align="right">Free shipping</td>
                    </tr>
                    @if ($discount > 0)
                      <tr>
                        <th>DISCOUNT</th>
                        <td align="right">-Rp{{ number_format($discount, 0, ',', '.') }}</td>
                      </tr>
                    @endif
                    <tr>
                      <th>VAT</th>
                      <td align="right">Rp{{ number_format($vat, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                      <th>TOTAL</th>
                      <td align="right">Rp{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="checkout__payment-methods">
                <div class="form-check">
                  <input class="form-check-input form-check-input_fill" type="radio" name="payment_method"
                    id="payment_midtrans" value="midtrans" checked>
                  <label class="form-check-label" for="payment_midtrans">
                    Midtrans
                    <p class="option-detail">
                      Pay securely via Midtrans — supports credit card, bank transfer, GoPay, OVO, and other
                      payment methods.
                    </p>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input form-check-input_fill" type="radio" name="payment_method"
                    id="payment_cod" value="cod">
                  <label class="form-check-label" for="payment_cod">
                    Cash on Delivery
                    <p class="option-detail">
                      Pay in cash when your order arrives at your doorstep. No additional fees.
                    </p>
                  </label>
                </div>
                <div class="policy-text">
                  Your personal data will be used to process your order, support your experience throughout this
                  website, and for other purposes described in our <a href="javascript:void(0)" target="_blank">privacy
                    policy</a>.
                </div>
              </div>
              <button type="button" class="btn btn-primary btn-checkout" id="btn-place-order">PLACE ORDER</button>
            </div>
          </div>
        </div>
      </form>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var select = document.getElementById('address-select');
      if (!select) return;

      var fields = {
        recipient_name: document.getElementById('recipient_name'),
        phone: document.getElementById('phone'),
        postal_code: document.getElementById('postal_code'),
        province: document.getElementById('province'),
        city: document.getElementById('city'),
        district: document.getElementById('district'),
        full_address: document.getElementById('full_address'),
      };

      var addressIdInput = document.getElementById('address_id');

      select.addEventListener('change', function() {
        var selected = select.options[select.selectedIndex];
        var addressId = selected.value || '';

        addressIdInput.value = addressId;

        if (!addressId) {
          for (var key in fields) {
            if (fields[key]) fields[key].value = '';
          }
          return;
        }

        var mapping = {
          recipient_name: 'recipient',
          phone: 'phone',
          postal_code: 'postal',
          province: 'province',
          city: 'city',
          district: 'district',
          full_address: 'full',
        };

        for (var fieldId in mapping) {
          var attr = 'data-' + mapping[fieldId];
          var val = selected.getAttribute(attr) || '';
          if (fields[fieldId]) fields[fieldId].value = val;
        }
      });
    });

    window.addEventListener('load', function() {
      $('.checkout-form .btn-checkout').off('click');

      document.getElementById('btn-place-order').addEventListener('click', function() {
        var form = document.querySelector('form[name="checkout-form"]');
        var payment = document.querySelector('input[name="payment_method"]:checked');
        var total = 'Rp{{ number_format($total, 0, ',', '.') }}';

        if (!document.getElementById('address_id').value) {
          Swal.fire({
            title: 'Address Required',
            text: 'Please select a shipping address before placing your order.',
            icon: 'warning',
            confirmButtonColor: '#B9A16B',
          });
          return;
        }

        Swal.fire({
          title: 'Confirm Order?',
          html: '<div style="text-align: left;">' +
            '<p><strong>Total Payment:</strong> ' + total + '</p>' +
            '<p><strong>Payment Method:</strong> ' + (payment ? payment.value.toUpperCase() : '-') + '</p>' +
            '<p><strong>Shipping:</strong> Free shipping</p>' +
            '</div>',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#B9A16B',
          confirmButtonText: 'Yes, Place Order!',
          cancelButtonText: 'Cancel',
        }).then(function(result) {
          if (result.isConfirmed) {
            var placeOrderBtn = document.getElementById('btn-place-order');
            if (placeOrderBtn) {
              placeOrderBtn.disabled = true;
              placeOrderBtn.textContent = 'Processing...';
            }
            form.submit();
          }
        });
      });
    });
  </script>
@endsection
