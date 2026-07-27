@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Order Details</h2>
      <div class="row">
        <x-customer-sidebar active="orders" />
        <div class="col-lg-9">
          {{-- Order Details --}}
          <div class="card border rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Order Details</h5>
              <div>
                <a class="btn btn-sm btn-outline-primary me-2" href="{{ route('customer.checkout.confirmation', $order->invoice_number) }}">
                  <i class="fa fa-file-text"></i> View Confirmation
                </a>
                <a class="btn btn-sm btn-outline-danger me-2" href="{{ route('customer.orders.invoice', $order->invoice_number) }}">
                  <i class="fa fa-file-pdf"></i> Download Invoice
                </a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.orders.index') }}">
                  <i class="fa fa-arrow-left"></i> Back
                </a>
              </div>
            </div>
            <div class="card-body p-0">
              @php
                $badge = match ($order->status) {
                    'pending' => 'bg-warning text-dark',
                    'processing' => 'bg-info text-white',
                    'shipped' => 'bg-primary text-white',
                    'completed' => 'bg-success text-white',
                    'cancelled' => 'bg-danger text-white',
                    default => 'bg-secondary text-white',
                };
              @endphp

              {{-- Mobile/tablet --}}
              <div class="d-lg-none p-3">
                <table class="table table-sm table-borderless mb-0">
                  <tbody>
                    <tr><th class="ps-0" style="width:40%">Order No</th><td class="pe-0 text-end">{{ $order->invoice_number }}</td></tr>
                    <tr><th class="ps-0">Recipient</th><td class="pe-0 text-end">{{ $order->recipient_name }}</td></tr>
                    <tr><th class="ps-0">Phone</th><td class="pe-0 text-end">{{ $order->phone }}</td></tr>
                    <tr><th class="ps-0">Order Date</th><td class="pe-0 text-end">{{ $order->created_at->format('d M Y H:i') }}</td></tr>
                    <tr><th class="ps-0">Payment</th><td class="pe-0 text-end">{{ ucfirst($order->payment_method) }}</td></tr>
                    <tr><th class="ps-0">Postal Code</th><td class="pe-0 text-end">{{ $order->postal_code }}</td></tr>
                    <tr><th class="ps-0">Status</th><td class="pe-0 text-end"><span class="badge {{ $badge }}">{{ ucfirst($order->status) }}</span></td></tr>
                  </tbody>
                </table>
              </div>

              {{-- Desktop --}}
              <div class="d-none d-lg-block table-responsive">
                <table class="table table-hover mb-0">
                  <tbody>
                    <tr>
                      <th style="width:15%">Order No</th>
                      <td style="width:35%">{{ $order->invoice_number }}</td>
                      <th style="width:15%">Recipient</th>
                      <td style="width:35%">{{ $order->recipient_name }}</td>
                    </tr>
                    <tr>
                      <th>Phone</th>
                      <td>{{ $order->phone }}</td>
                      <th>Order Date</th>
                      <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                      <th>Payment Method</th>
                      <td>{{ ucfirst($order->payment_method) }}</td>
                      <th>Postal Code</th>
                      <td>{{ $order->postal_code }}</td>
                    </tr>
                    <tr>
                      <th>Order Status</th>
                      <td colspan="3">
                        <span class="badge {{ $badge }}">{{ ucfirst($order->status) }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- Ordered Items --}}
          <div class="card border rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white">
              <h5 class="mb-0">Ordered Items</h5>
            </div>
            <div class="card-body p-0">
              {{-- Mobile/tablet: cards --}}
              <div class="d-lg-none p-3">
                @foreach ($order->items as $item)
                  <div class="card mb-2 border rounded-3 shadow-sm">
                    <div class="card-body p-3">
                      <div class="d-flex gap-3">
                        <img src="{{ $item->image ?? asset('assets/images/product-placeholder.png') }}"
                          alt="{{ $item->product_name }}" style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                        <div class="flex-grow-1 min-w-0">
                          <div class="fw-medium text-truncate">{{ $item->product_name }}</div>
                          <div class="small text-muted mt-1">
                            Rp{{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}
                          </div>
                          <div class="fw-semibold mt-1">
                            Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                          </div>
                          <div class="small text-muted mt-1">
                            {{ $item->category_name ?? '-' }} · {{ $item->brand_name ?? '-' }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>

              {{-- Desktop: table --}}
              <div class="d-none d-lg-block table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Product</th>
                      <th class="text-center">Price</th>
                      <th class="text-center">Quantity</th>
                      <th class="text-center">Subtotal</th>
                      <th class="text-center">Category</th>
                      <th class="text-center">Brand</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($order->items as $item)
                      <tr>
                        <td>
                          <div class="d-flex align-items-center gap-3">
                            <img src="{{ $item->image ?? asset('assets/images/product-placeholder.png') }}"
                              alt="{{ $item->product_name }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                            <span class="fw-medium">{{ $item->product_name }}</span>
                          </div>
                        </td>
                        <td class="text-center">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center fw-semibold">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->category_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->brand_name ?? '-' }}</td>
                        <td class="text-center">
                          @if ($item->product)
                            <a href="{{ route('products.show', $item->product->slug) }}" class="btn btn-sm btn-outline-primary">
                              <i class="fa fa-eye"></i>
                            </a>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- Shipping Address --}}
          <div class="card border rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white">
              <h5 class="mb-0">Shipping Address</h5>
            </div>
            <div class="card-body">
              <div class="my-account__address-item">
                <div class="my-account__address-item__detail">
                  <p class="fw-medium mb-1">{{ $order->recipient_name }}</p>
                  <p class="mb-1">{{ $order->full_address }}</p>
                  <p class="mb-1">{{ $order->city }}, {{ $order->province }}</p>
                  <p class="mb-1">{{ $order->district }}</p>
                  <p class="mb-1">{{ $order->postal_code }}</p>
                  <p class="mb-0">Phone: {{ $order->phone }}</p>
                </div>
              </div>
            </div>
          </div>

          {{-- Transactions --}}
          <div class="card border rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white">
              <h5 class="mb-0">Transactions</h5>
            </div>
            <div class="card-body p-0">
              @php
                $payBadge = match ($order->payment_status) {
                    'paid' => 'bg-success',
                    'pending' => 'bg-warning text-dark',
                    'failed' => 'bg-danger',
                    'unpaid' => 'bg-secondary',
                    default => 'bg-secondary',
                };
              @endphp

              {{-- Mobile/tablet --}}
              <div class="d-lg-none p-3">
                <table class="table table-sm table-borderless mb-0">
                  <tbody>
                    <tr><th class="ps-0" style="width:40%">Subtotal</th><td class="pe-0 text-end">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td></tr>
                    <tr><th class="ps-0">Shipping</th><td class="pe-0 text-end">@if ($order->shipping_cost > 0) Rp{{ number_format($order->shipping_cost, 0, ',', '.') }} @else Free @endif</td></tr>
                    <tr><th class="ps-0">Discount</th><td class="pe-0 text-end">@if ($order->coupon_discount > 0) -Rp{{ number_format($order->coupon_discount, 0, ',', '.') }} @else Rp0 @endif</td></tr>
                    <tr><th class="ps-0 border-0">Total</th><td class="pe-0 text-end border-0 fw-bold fs-5">Rp{{ number_format($order->total, 0, ',', '.') }}</td></tr>
                    <tr><th class="ps-0">Payment</th><td class="pe-0 text-end">{{ ucfirst($order->payment_method) }}</td></tr>
                    <tr><th class="ps-0">Status</th><td class="pe-0 text-end"><span class="badge {{ $payBadge }}">{{ ucfirst($order->payment_status) }}</span></td></tr>
                  </tbody>
                </table>
              </div>

              {{-- Desktop --}}
              <div class="d-none d-lg-block table-responsive">
                <table class="table table-hover mb-0">
                  <tbody>
                    <tr>
                      <th style="width:15%">Subtotal</th>
                      <td style="width:35%">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
                      <th style="width:15%">Shipping</th>
                      <td style="width:35%">
                        @if ($order->shipping_cost > 0)
                          Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                        @else
                          Free
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Discount</th>
                      <td>
                        @if ($order->coupon_discount > 0)
                          -Rp{{ number_format($order->coupon_discount, 0, ',', '.') }}
                        @else
                          Rp0
                        @endif
                      </td>
                      <th>Payment Mode</th>
                      <td>{{ ucfirst($order->payment_method) }}</td>
                    </tr>
                    <tr>
                      <th class="border-0">Total</th>
                      <td class="border-0 fw-bold fs-5">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                      <th class="border-0">Status</th>
                      <td class="border-0">
                        <span class="badge {{ $payBadge }}">{{ ucfirst($order->payment_status) }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          @if ($order->status === 'pending')
            <div class="card border rounded-3 shadow-sm">
              <div class="card-body text-end">
                <form action="{{ route('customer.orders.cancel', $order->invoice_number) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">
                    <i class="fa fa-times"></i> Cancel Order
                  </button>
                </form>
              </div>
            </div>
          @endif
        </div>
      </div>
    </section>
  </main>
@endsection