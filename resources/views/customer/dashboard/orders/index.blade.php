@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">My Orders</h2>
      <div class="row">
        <x-customer-sidebar active="orders" />
        <div class="col-lg-9">
          <div class="page-content my-account__orders">
            @if ($orders->isEmpty())
              <p>No orders yet.</p>
            @else
              {{-- Mobile/tablet cards --}}
              <div class="d-lg-none">
                @foreach ($orders as $order)
                  <div class="card mb-3 border rounded-3 shadow-sm">
                    <div class="card-body p-3">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong class="text-primary" style="font-size: 0.85rem;">{{ $order->invoice_number }}</strong>
                        @php
                          $badge = match ($order->status) {
                              'pending' => 'bg-warning',
                              'processing' => 'bg-info',
                              'completed' => 'bg-success',
                              'cancelled' => 'bg-danger',
                              default => 'bg-secondary',
                          };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($order->status) }}</span>
                      </div>
                      <div class="small text-muted mb-1">
                        <span><strong>Recipient:</strong> {{ $order->recipient_name }}</span>
                        <span class="ms-3"><strong>Phone:</strong> {{ $order->phone }}</span>
                      </div>
                      <div class="small text-muted mb-1">
                        <span><strong>Total:</strong> Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        <span class="ms-3"><strong>Payment:</strong> {{ ucfirst($order->payment_method) }}</span>
                        @php
                          $payBadge = match ($order->payment_status) {
                              'paid' => 'bg-success',
                              'pending' => 'bg-warning',
                              'failed' => 'bg-danger',
                              'unpaid' => 'bg-secondary',
                              default => 'bg-secondary',
                          };
                        @endphp
                        <span class="badge {{ $payBadge }} ms-1">{{ ucfirst($order->payment_status) }}</span>
                      </div>
                      <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                        <span class="small text-muted">{{ $order->created_at->format('d/m/Y') }} ·
                          {{ $order->items_count }} items</span>
                        <a href="{{ route('customer.orders.show', $order->invoice_number) }}"
                          class="btn btn-sm btn-outline-primary">
                          <i class="fa fa-eye"></i> Detail
                        </a>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>

              {{-- Desktop table --}}
              <div class="d-none d-lg-block table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>Invoice</th>
                      <th>Recipient</th>
                      <th class="text-center">Phone</th>
                      <th class="text-center">Subtotal</th>
                      <th class="text-center">Shipping</th>
                      <th class="text-center">Total</th>
                      <th class="text-center">Payment</th>
                      <th class="text-center">Payment Status</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Date</th>
                      <th class="text-center">Items</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($orders as $order)
                      <tr>
                        <td><strong>{{ $order->invoice_number }}</strong></td>
                        <td>{{ $order->recipient_name }}</td>
                        <td class="text-center">{{ $order->phone }}</td>
                        <td class="text-center">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
                        <td class="text-center">
                          @if ($order->shipping_cost > 0)
                            Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                          @else
                            Free
                          @endif
                        </td>
                        <td class="text-center">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="text-center">{{ ucfirst($order->payment_method) }}</td>
                        <td class="text-center">
                          @php
                          $payBadge = match ($order->payment_status) {
                              'paid' => 'bg-success',
                              'pending' => 'bg-warning',
                              'failed' => 'bg-danger',
                              'unpaid' => 'bg-secondary',
                              default => 'bg-secondary',
                          };
                        @endphp
                        <span class="badge {{ $payBadge }}">{{ ucfirst($order->payment_status) }}</span>
                        </td>
                        <td class="text-center">
                          @php
                            $badge = match ($order->status) {
                                'pending' => 'bg-warning',
                                'processing' => 'bg-info',
                                'completed' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                          @endphp
                          <span class="badge {{ $badge }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="text-center">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $order->items_count }}</td>
                        <td class="text-center">
                          <a href="{{ route('customer.orders.show', $order->invoice_number) }}">
                            <div class="list-icon-function view-icon">
                              <div class="item eye">
                                <i class="fa fa-eye"></i>
                              </div>
                            </div>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-center mt-4">
                {{ $orders->links('components.pagination-custom') }}
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
