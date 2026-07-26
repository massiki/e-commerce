@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Order Details</h2>
      <div class="row">
        <x-customer-sidebar active="orders" />
        <div class="col-lg-9">
          <div class="wg-box mt-3 mb-5">
            <div class="row">
              <div class="col-6">
                <h5>Ordered Details</h5>
              </div>
              <div class="col-6 text-end">
                <a class="btn btn-sm btn-outline-dark me-2" href="{{ route('customer.checkout.confirmation', $order->invoice_number) }}">View Confirmation</a>
                <a class="btn btn-sm btn-danger" href="{{ route('customer.orders.index') }}">Back</a>
              </div>
            </div>

            {{-- Mobile/tablet: info card --}}
            @php
              $badge = match ($order->status) {
                  'pending' => 'bg-warning',
                  'processing' => 'bg-info',
                  'completed' => 'bg-success',
                  'cancelled' => 'bg-danger',
                  default => 'bg-secondary',
              };
            @endphp
            <div class="d-lg-none">
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

            {{-- Desktop: table --}}
            <div class="d-none d-lg-block table-responsive">
              <table class="table table-striped table-bordered table-transaction">
                <tbody>
                  <tr>
                    <th>Order No</th>
                    <td>{{ $order->invoice_number }}</td>
                    <th>Recipient</th>
                    <td>{{ $order->recipient_name }}</td>
                    <th>Phone</th>
                    <td>{{ $order->phone }}</td>
                  </tr>
                  <tr>
                    <th>Order Date</th>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    <th>Payment Method</th>
                    <td>{{ ucfirst($order->payment_method) }}</td>
                    <th>Postal Code</th>
                    <td>{{ $order->postal_code }}</td>
                  </tr>
                  <tr>
                    <th>Order Status</th>
                    <td colspan="5">
                      <span class="badge {{ $badge }}">{{ ucfirst($order->status) }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="wg-box wg-table table-all-user">
            <div class="row">
              <div class="col-6">
                <h5>Ordered Items</h5>
              </div>
            </div>

            {{-- Mobile/tablet: cards --}}
            <div class="d-lg-none">
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
              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>Name</th>
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
                      <td class="pname">
                        <div class="image">
                          <img src="{{ $item->image ?? asset('assets/images/product-placeholder.png') }}"
                            alt="{{ $item->product_name }}" class="image" width="90">
                        </div>
                        <div class="name">
                          <span class="body-title-2">{{ $item->product_name }}</span>
                        </div>
                      </td>
                      <td class="text-center">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                      <td class="text-center">{{ $item->quantity }}</td>
                      <td class="text-center">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                      <td class="text-center">{{ $item->category_name ?? '-' }}</td>
                      <td class="text-center">{{ $item->brand_name ?? '-' }}</td>
                      <td class="text-center">
                        <div class="list-icon-function view-icon">
                          <a href="{{ route('products.show', $item->product?->slug) }}" class="item eye">
                            <i class="fa fa-eye"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="wg-box mt-5">
            <h5>Shipping Address</h5>
            <div class="my-account__address-item">
              <div class="my-account__address-item__detail">
                <p>{{ $order->recipient_name }}</p>
                <p>{{ $order->full_address }}</p>
                <p>{{ $order->city }}, {{ $order->province }}</p>
                <p>{{ $order->district }}</p>
                <p>{{ $order->postal_code }}</p>
                <br>
                <p>Phone: {{ $order->phone }}</p>
              </div>
            </div>
          </div>

          <div class="wg-box mt-5">
            <h5>Transactions</h5>

            @php
              $payBadge = match ($order->payment_status) {
                  'paid' => 'bg-success',
                  'pending' => 'bg-warning',
                  'failed' => 'bg-danger',
                  'unpaid' => 'bg-secondary',
                  default => 'bg-secondary',
              };
            @endphp

            {{-- Mobile/tablet: info card --}}
            <div class="d-lg-none">
              <table class="table table-sm table-borderless mb-0">
                <tbody>
                  <tr><th class="ps-0" style="width:40%">Subtotal</th><td class="pe-0 text-end">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td></tr>
                  <tr><th class="ps-0">Shipping</th><td class="pe-0 text-end">@if ($order->shipping_cost > 0) Rp{{ number_format($order->shipping_cost, 0, ',', '.') }} @else Free @endif</td></tr>
                  <tr><th class="ps-0">Discount</th><td class="pe-0 text-end">@if ($order->coupon_discount > 0) -Rp{{ number_format($order->coupon_discount, 0, ',', '.') }} @else Rp0 @endif</td></tr>
                  <tr><th class="ps-0">Total</th><td class="pe-0 text-end fw-bold">Rp{{ number_format($order->total, 0, ',', '.') }}</td></tr>
                  <tr><th class="ps-0">Payment</th><td class="pe-0 text-end">{{ ucfirst($order->payment_method) }}</td></tr>
                  <tr><th class="ps-0">Status</th><td class="pe-0 text-end"><span class="badge {{ $payBadge }}">{{ ucfirst($order->payment_status) }}</span></td></tr>
                </tbody>
              </table>
            </div>

            {{-- Desktop: table --}}
            <div class="d-none d-lg-block table-responsive">
              <table class="table table-striped table-bordered table-transaction">
                <tbody>
                  <tr>
                    <th>Subtotal</th>
                    <td>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    <th>Shipping</th>
                    <td>
                      @if ($order->shipping_cost > 0)
                        Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                      @else
                        Free
                      @endif
                    </td>
                    <th>Discount</th>
                    <td>
                      @if ($order->coupon_discount > 0)
                        -Rp{{ number_format($order->coupon_discount, 0, ',', '.') }}
                      @else
                        Rp0
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <th>Total</th>
                    <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                    <th>Payment Mode</th>
                    <td>{{ ucfirst($order->payment_method) }}</td>
                    <th>Status</th>
                    <td>
                      <span class="badge {{ $payBadge }}">{{ ucfirst($order->payment_status) }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          @if ($order->status === 'pending')
            <div class="wg-box mt-5 text-end">
              <form action="{{ route('customer.orders.cancel', $order->invoice_number) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</button>
              </form>
            </div>
          @endif
        </div>
      </div>
    </section>
  </main>
@endsection
