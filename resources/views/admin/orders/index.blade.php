@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Orders</h3>
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
            <div class="text-tiny">Orders</div>
          </li>
        </ul>
      </div>

      <div class="wg-box">
        <div class="flex items-center justify-between gap10 flex-wrap">
          <div class="wg-filter flex-grow">
            <form class="form-search" method="GET" action="{{ route('admin.orders.index') }}">
              <fieldset class="name">
                <input type="text" placeholder="Search here..." class="" name="search" tabindex="2"
                  value="{{ request('search') }}">
              </fieldset>
              <div class="button-submit">
                <button class="" type="submit"><i class="icon-search"></i></button>
              </div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-order">
            <thead>
              <tr>
                <th class="text-center">Invoice No</th>
                <th class="text-center">Name</th>
                <th class="text-center">Phone</th>
                <th class="text-center">Total</th>
                <th class="text-center">Payment Method</th>
                <th class="text-center">Payment Status</th>
                <th class="text-center">Status</th>
                <th class="text-center">Order Date</th>
                <th class="text-center">Items</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($orders as $order)
                <tr>
                  <td class="text-center">{{ $order->invoice_number }}</td>
                  <td class="text-center">{{ $order->recipient_name }}</td>
                  <td class="text-center">{{ $order->phone }}</td>
                  <td class="text-center">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                  <td class="text-center">{{ $order->payment_method }}</td>
                  <td class="text-center">
                    @php
                      $paymentBadge = match ($order->payment_status) {
                          'paid' => 'bg-success',
                          'pending' => 'bg-warning text-dark',
                          'challenge' => 'bg-warning text-dark',
                          'failed' => 'bg-danger',
                          'unpaid' => 'bg-secondary',
                          default => 'bg-secondary',
                      };
                    @endphp
                    <span class="badge {{ $paymentBadge }}">{{ ucfirst($order->payment_status) }}</span>
                  </td>
                  <td class="text-center">
                    @php
                      $statusBadge = match ($order->status) {
                          'completed' => 'bg-success',
                          'processing' => 'bg-info',
                          'shipped' => 'bg-primary',
                          'pending' => 'bg-warning text-dark',
                          'cancelled' => 'bg-danger',
                          default => 'bg-secondary',
                      };
                    @endphp
                    <span class="badge {{ $statusBadge }}">{{ ucfirst($order->status) }}</span>
                  </td>
                  <td class="text-center">{{ $order->created_at->format('H:i d-M-Y ') }}</td>
                  <td class="text-center">{{ $order->items->count() }}</td>
                  <td class="text-center">
                    <a href="{{ route('admin.orders.show', $order->id) }}">
                      <div class="list-icon-function view-icon">
                        <div class="item eye">
                          <i class="icon-eye"></i>
                        </div>
                      </div>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="11" class="text-center">No orders found</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="divider"></div>
        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
          {{ $orders->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>
@endsection
