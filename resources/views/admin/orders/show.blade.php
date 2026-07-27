@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Order Details</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li>
            <a href="{{ route('admin.dashboard') }}">
              <div class="text-tiny">Dashboard</div>
            </a>
          </li>
          <li>
            <i class="icon-chevron-right"></i>
          </li>
          <li>
            <a href="{{ route('admin.orders.index') }}">
              <div class="text-tiny">Orders</div>
            </a>
          </li>
          <li>
            <i class="icon-chevron-right"></i>
          </li>
          <li>
            <div class="text-tiny">Detail</div>
          </li>
        </ul>
      </div>

      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <div class="wg-box">
        <div class="row">
          <div class="col-6">
            <h5>Order Information</h5>
          </div>
          <div class="col-6 text-end">
            <a class="btn btn-sm btn-primary me-2" href="{{ route('admin.orders.shipping-label', $order) }}">
              <i class="icon-download"></i> Shipping Label
            </a>
            <a class="btn btn-sm btn-danger" href="{{ route('admin.orders.index') }}">Back</a>
          </div>
        </div>
        <div class="table-responsive mt-3">
          <table class="table table-striped table-bordered">
            <tbody>
              <tr>
                <th>Order No</th>
                <td>{{ $order->invoice_number }}</td>
                <th>Order Date</th>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
              </tr>
              <tr>
                <th>Recipient</th>
                <td>{{ $order->recipient_name }}</td>
                <th>Phone</th>
                <td>{{ $order->phone }}</td>
              </tr>
              <tr>
                <th>Payment Method</th>
                <td>{{ ucfirst($order->payment_method) }}</td>
                <th>Coupon</th>
                <td>{{ $order->coupon_code ?? '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="wg-box mt-4">
        <h5>Shipping Address</h5>
        <div class="table-responsive mt-3">
          <table class="table table-striped table-bordered">
            <tbody>
              <tr>
                <th>City</th>
                <td>{{ $order->city }}</td>
                <th>Province</th>
                <td>{{ $order->province }}</td>
              </tr>
              <tr>
                <th>District</th>
                <td>{{ $order->district }}</td>
                <th>Postal Code</th>
                <td>{{ $order->postal_code }}</td>
              </tr>
              <tr>
                <th>Address</th>
                <td colspan="3">{{ $order->full_address }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="wg-box mt-4">
        <h5>Order Items</h5>
        <div class="table-responsive mt-3">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Product</th>
                <th class="text-center">Price</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Subtotal</th>
                <th class="text-center">Category</th>
                <th class="text-center">Brand</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($order->items as $item)
                <tr>
                  <td class="pname">
                    <div class="image">
                      <img src="{{ $item->image ?? asset('admin/images/product-placeholder.png') }}"
                        alt="{{ $item->product_name }}" class="image">
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
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="wg-box mt-4">
        <h5>Transaction Summary</h5>
        <div class="table-responsive mt-3">
          <table class="table table-striped table-bordered" style="max-width:500px;">
            <tbody>
              <tr>
                <th style="width:200px;">Subtotal</th>
                <td>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
              </tr>
              <tr>
                <th>Shipping</th>
                <td>
                  @if ($order->shipping_cost > 0)
                    Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                  @else
                    Free
                  @endif
                </td>
              </tr>
              @if ($order->coupon_discount > 0)
                <tr>
                  <th>Discount</th>
                  <td>-Rp{{ number_format($order->coupon_discount, 0, ',', '.') }}</td>
                </tr>
              @endif
              <tr>
                <th>Total</th>
                <td><strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="wg-box mt-4">
        <h5>Update Order Status</h5>
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
          @csrf
          @method('PUT')
          <div class="row mt-3">
            <div class="col-md-6">
              <div class="body-title mb-10">Order Status</div>
              <div class="select mb-10">
                <select name="status" class="">
                  <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                  <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                  <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                  <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
              </div>
              @error('status')
                <div class="text-danger mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <div class="body-title mb-10">Payment Status</div>
              <div class="select mb-10">
                <select name="payment_status" class="">
                  <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                  <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                  <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
              </div>
              @error('payment_status')
                <div class="text-danger mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mt-3">
            <button type="submit" class="tf-button">Update Status</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
