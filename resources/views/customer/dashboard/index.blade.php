@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">My Account</h2>
      <div class="row">
        <x-customer-sidebar active="dashboard" />
        <div class="col-lg-9">
          <div class="page-content my-account__dashboard">
            <p class="welcome-text">Hello <strong>{{ Auth::user()->name }}</strong></p>

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="card border rounded-3 shadow-sm h-100">
                  <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="fs-1 text-primary"><i class="fa fa-shopping-bag"></i></div>
                    <div>
                      <div class="text-muted small">Total Orders</div>
                      <div class="fw-bold fs-5">{{ $totalOrders }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border rounded-3 shadow-sm h-100">
                  <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="fs-1 text-success"><i class="fa fa-dollar"></i></div>
                    <div>
                      <div class="text-muted small">Total Spending</div>
                      <div class="fw-bold fs-5">Rp{{ number_format($totalSpending, 0, ',', '.') }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border rounded-3 shadow-sm h-100">
                  <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="fs-1 text-danger"><i class="fa fa-heart"></i></div>
                    <div>
                      <div class="text-muted small">Wishlist</div>
                      <div class="fw-bold fs-5">{{ $wishlistCount }} items</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card border rounded-3 shadow-sm mb-4">
              <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="{{ route('customer.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
              </div>
              <div class="card-body p-0">
                @if ($recentOrders->isEmpty())
                  <p class="text-muted p-3 mb-0">No orders yet.</p>
                @else
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th>Invoice</th>
                          <th>Status</th>
                          <th>Total</th>
                          <th>Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($recentOrders as $order)
                          <tr>
                            <td>
                              <a href="{{ route('customer.orders.show', $order->invoice_number) }}" class="text-primary">
                                {{ $order->invoice_number }}
                              </a>
                            </td>
                            <td>
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
                              <span class="badge {{ $badge }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif
              </div>
            </div>

            @if ($notifications->isNotEmpty())
              <div class="card border rounded-3 shadow-sm">
                <div class="card-header bg-white">
                  <h5 class="mb-0">Notifications</h5>
                </div>
                <div class="card-body p-0">
                  <ul class="list-group list-group-flush">
                    @foreach ($notifications as $notification)
                      <li class="list-group-item d-flex align-items-center gap-3">
                        <i class="fa fa-bell text-muted"></i>
                        <div>
                          <div>{{ $notification->message ?? $notification->title ?? 'Notification' }}</div>
                          <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                      </li>
                    @endforeach
                  </ul>
                </div>
              </div>
            @endif

          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
