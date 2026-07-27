@extends('layouts.admin')
@section('content')
  <div class="main-content-inner">

    <div class="main-content-wrap">
      <div class="tf-section-2 mb-30">
        <div class="flex gap20 flex-wrap-mobile">
          <div class="w-half">

            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-shopping-bag"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Total Orders</div>
                    <h4>{{ $totalOrders }}</h4>
                  </div>
                </div>
              </div>
            </div>

            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-dollar-sign"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Total Amount</div>
                    <h4>Rp{{ number_format($totalAmount, 0, ',', '.') }}</h4>
                  </div>
                </div>
              </div>
            </div>

            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-shopping-bag"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Pending Orders</div>
                    <h4>{{ $pendingOrders }}</h4>
                  </div>
                </div>
              </div>
            </div>

            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-dollar-sign"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Pending Orders Amount</div>
                    <h4>Rp{{ number_format($pendingAmount, 0, ',', '.') }}</h4>
                  </div>
                </div>
              </div>
            </div>

            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-shopping-bag"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Total Products</div>
                    <h4>{{ $totalProducts }}</h4>
                  </div>
                </div>
              </div>
            </div>

            <div class="wg-chart-default">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-user"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Total Customers</div>
                    <h4>{{ $totalCustomers }}</h4>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="w-half">

            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-shopping-bag"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Completed Orders</div>
                    <h4>{{ $completedOrders }}</h4>
                  </div>
                </div>
              </div>
            </div>


            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-dollar-sign"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Completed Orders Amount</div>
                    <h4>Rp{{ number_format($completedAmount, 0, ',', '.') }}</h4>
                  </div>
                </div>
              </div>
            </div>


            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-shopping-bag"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Shipped Orders</div>
                    <h4>{{ $shippedOrders }}</h4>
                  </div>
                </div>
              </div>
            </div>


            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-dollar-sign"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Shipped Orders Amount</div>
                    <h4>Rp{{ number_format($shippedAmount, 0, ',', '.') }}</h4>
                  </div>
                </div>
              </div>
            </div>


            <div class="wg-chart-default mb-20">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-shopping-bag"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Canceled Orders</div>
                    <h4>{{ $canceledOrders }}</h4>
                  </div>
                </div>
              </div>
            </div>


            <div class="wg-chart-default">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap14">
                  <div class="image ic-bg">
                    <i class="icon-dollar-sign"></i>
                  </div>
                  <div>
                    <div class="body-text mb-2">Canceled Orders Amount</div>
                    <h4>Rp{{ number_format($canceledAmount, 0, ',', '.') }}</h4>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>

        <div class="wg-box">
          <div class="flex items-center justify-between">
            <h5>Earnings revenue</h5>
            <div class="dropdown default">
              <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="icon-more"><i class="icon-more-horizontal"></i></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a href="#" class="chart-period" data-period="thisYear">This Year</a>
                </li>
                <li>
                  <a href="#" class="chart-period" data-period="thisWeek">This Week</a>
                </li>
                <li>
                  <a href="#" class="chart-period" data-period="lastWeek">Last Week</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="flex flex-wrap gap40">
            <div>
              <div class="mb-2">
                <div class="block-legend">
                  <div class="dot t1"></div>
                  <div class="text-tiny">Revenue</div>
                </div>
              </div>
              <div class="flex items-center gap10">
                <h4>Rp{{ number_format($currentRevenue, 0, ',', '.') }} </h4>
                <div class="box-icon-trending {{ $revenueGrowth >= 0 ? 'up' : 'down' }}">
                  <i class="{{ $revenueGrowth >= 0 ? 'icon-trending-up' : 'icon-trending-down' }}"></i>
                  <div class="body-title number">{{ $revenueGrowth }}%</div>
                </div>
              </div>
            </div>
            <div>
              <div class="mb-2">
                <div class="block-legend">
                  <div class="dot t2"></div>
                  <div class="text-tiny">Total Orders this Month</div>
                </div>
              </div>
              <div class="flex items-center gap10">
                <h4>{{ $currentOrderCount }}</h4>
                <div class="box-icon-trending {{ $orderGrowth >= 0 ? 'up' : 'down' }}">
                  <i class="{{ $orderGrowth >= 0 ? 'icon-trending-up' : 'icon-trending-down' }}"></i>
                  <div class="body-title number">{{ $orderGrowth }}%</div>
                </div>
              </div>
            </div>
          </div>
          <div id="line-chart-8" style="min-height: 350px; width: 100%;"></div>
        </div>

      </div>
      <div class="tf-section mb-30">

        <div class="wg-box">
          <div class="flex items-center justify-between">
            <h5>Recent orders</h5>
            <div class="dropdown default">
              <a class="btn btn-secondary dropdown-toggle" href="{{ route('admin.orders.index') }}">
                <span class="view-all">View all</span>
              </a>
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
                @forelse ($recentOrders as $order)
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
                    <td class="text-center">{{ $order->created_at->format('H:i d-M-Y') }}</td>
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
                    <td colspan="10" class="text-center">No orders found</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <div class="wg-box">
          <div class="flex items-center justify-between">
            <h5>Best Selling Products</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-order">
              <thead>
                <tr>
                  <th class="text-center" style="width: 50px;">No</th>
                  <th>Product</th>
                  <th class="text-center">Total Sold</th>
                  <th class="text-center">Total Revenue</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($bestSellingProducts as $index => $item)
                  <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                      <div class="flex items-center gap14">
                        <div class="image no-bg" style="width: 50px; height: 50px;">
                          <img src="{{ $item->image }}" alt="{{ $item->product_name }}"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                        </div>
                        <div>{{ $item->product_name }}</div>
                      </div>
                    </td>
                    <td class="text-center">{{ $item->total_sold }}</td>
                    <td class="text-center">Rp{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center">No products sold yet</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

  </div>
@endsection

@push('scripts')
  <script>
    var chartData = {{ Js::from($chartData) }};

    (function($) {
      var chart = null;
      var currentPeriod = 'thisYear';

      function renderChart(period) {
        var data = chartData[period];
        var options = {
          series: data.series,
          chart: {
            type: 'bar',
            height: '100%',
            toolbar: {
              show: false
            },
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: '10px',
              endingShape: 'rounded'
            },
          },
          dataLabels: {
            enabled: false
          },
          legend: {
            show: false
          },
          colors: ['#22C55E', '#EF4444'],
          stroke: {
            show: false
          },
          xaxis: {
            labels: {
              style: {
                colors: '#212529'
              }
            },
            categories: data.categories,
          },
          yaxis: {
            show: false
          },
          fill: {
            opacity: 1
          },
          tooltip: {
            y: {
              formatter: function(val) {
                return "Rp " + Number(val).toLocaleString('id-ID') + ""
              }
            }
          }
        };

        if (chart) {
          chart.updateOptions(options);
        } else {
          chart = new ApexCharts(document.querySelector("#line-chart-8"), options);
          if ($("#line-chart-8").length > 0) {
            chart.render();
          }
        }
      }

      $(document).on('click', '.chart-period', function(e) {
        e.preventDefault();
        var period = $(this).data('period');
        if (period !== currentPeriod) {
          currentPeriod = period;
          renderChart(period);
        }
      });

      jQuery(window).on("load", function() {
        renderChart('thisYear');
      });
    })(jQuery);
  </script>
@endpush
