<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Invoice {{ $order->invoice_number }}</title>
  <style>
    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 12px;
      color: #333;
      margin: 0;
      padding: 30px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #B9A16B;
      padding-bottom: 20px;
      margin-bottom: 20px;
    }

    .header-left h1 {
      margin: 0;
      font-size: 24px;
      color: #B9A16B;
    }

    .header-left p {
      margin: 4px 0 0;
      color: #666;
      font-size: 11px;
    }

    .header-right {
      text-align: right;
    }

    .header-right h2 {
      margin: 0;
      font-size: 28px;
      color: #333;
      letter-spacing: 4px;
    }

    .header-right p {
      margin: 4px 0 0;
      color: #666;
      font-size: 11px;
    }

    .info-section {
      display: flex;
      justify-content: space-between;
      margin-bottom: 24px;
    }

    .info-box {
      width: 32%;
    }

    .info-box h4 {
      margin: 0 0 8px;
      font-size: 13px;
      color: #B9A16B;
      border-bottom: 1px solid #eee;
      padding-bottom: 4px;
    }

    .info-box p {
      margin: 2px 0;
      font-size: 11px;
      line-height: 1.5;
    }

    .info-box .label {
      color: #888;
    }

    table.items {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    table.items thead th {
      background: #B9A16B;
      color: #fff;
      font-size: 11px;
      padding: 8px 10px;
      text-align: left;
    }

    table.items thead th.text-right {
      text-align: right;
    }

    table.items thead th.text-center {
      text-align: center;
    }

    table.items tbody td {
      padding: 8px 10px;
      border-bottom: 1px solid #eee;
      font-size: 11px;
    }

    table.items tbody td.text-right {
      text-align: right;
    }

    table.items tbody td.text-center {
      text-align: center;
    }

    .summary {
      width: 300px;
      margin-left: auto;
      border-collapse: collapse;
    }

    .summary td {
      padding: 6px 10px;
      font-size: 11px;
    }

    .summary td.label {
      text-align: right;
      color: #666;
    }

    .summary td.value {
      text-align: right;
      font-weight: bold;
    }

    .summary tr.total td {
      border-top: 2px solid #333;
      font-size: 14px;
      padding-top: 8px;
    }

    .summary tr.total td.value {
      color: #B9A16B;
    }

    .status-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 3px;
      font-size: 10px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .status-paid {
      background: #28a745;
      color: #fff;
    }

    .status-unpaid {
      background: #6c757d;
      color: #fff;
    }

    .status-pending {
      background: #ffc107;
      color: #333;
    }

    .status-failed {
      background: #dc3545;
      color: #fff;
    }

    .footer {
      margin-top: 40px;
      padding-top: 16px;
      border-top: 1px solid #eee;
      text-align: center;
      font-size: 10px;
      color: #999;
    }

    .footer p {
      margin: 2px 0;
    }
  </style>
</head>

<body>

  <div class="header">
    <div class="header-left">
      <h1>{{ config('app.name') }}</h1>
      <p>Kp. Cigadog Rt.01 Rw.10 Desa Lingkungpasir Kec. Cibiuk Kab. Garut</p>
      <p>Email: fikri.amrulloh15@gmail.com | Telp: 085294532451</p>
    </div>
    <div class="header-right">
      <h2>INVOICE</h2>
      <p>#{{ $order->invoice_number }}</p>
      <p>{{ $order->created_at->format('d/m/Y') }}</p>
    </div>
  </div>

  <div class="info-section">
    <div class="info-box">
      <h4>Bill To</h4>
      <p><strong>{{ $order->recipient_name }}</strong></p>
      <p>{{ $order->full_address }}</p>
      <p>{{ $order->city }}, {{ $order->province }}</p>
      <p>{{ $order->district }} - {{ $order->postal_code }}</p>
      <p>Phone: {{ $order->phone }}</p>
    </div>
    <div class="info-box">
      <h4>Payment Details</h4>
      <p><span class="label">Method:</span> {{ ucfirst($order->payment_method) }}</p>
      <p><span class="label">Status:</span>
        <span class="status-badge status-{{ $order->payment_status }}">
          {{ ucfirst($order->payment_status) }}
        </span>
      </p>
      <p><span class="label">Order Status:</span> {{ ucfirst($order->status) }}</p>
    </div>
    <div class="info-box">
      <h4>Shipping Information</h4>
      <p><span class="label">Courier:</span> JNE Reguler</p>
      <p><span class="label">Tracking No:</span> JNE2398476123</p>
      <p><span class="label">Estimation:</span> 3 - 5 Hari Kerja</p>
      <p><span class="label">Shipping Cost:</span>
        @if ($order->shipping_cost > 0)
          Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
        @else
          Free
        @endif
      </p>
    </div>
  </div>

  <table class="items">
    <thead>
      <tr>
        <th style="width:50%">Product</th>
        <th class="text-center">Price</th>
        <th class="text-center">Qty</th>
        <th class="text-right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($order->items as $item)
        <tr>
          <td>{{ $item->product_name }}</td>
          <td class="text-center">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
          <td class="text-center">{{ $item->quantity }}</td>
          <td class="text-right">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <table class="summary">
    <tr>
      <td class="label">Subtotal</td>
      <td class="value">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
    </tr>
    <tr>
      <td class="label">Shipping</td>
      <td class="value">
        @if ($order->shipping_cost > 0)
          Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
        @else
          Free
        @endif
      </td>
    </tr>
    @if ($order->coupon_discount > 0)
      <tr>
        <td class="label">Discount</td>
        <td class="value">-Rp{{ number_format($order->coupon_discount, 0, ',', '.') }}</td>
      </tr>
    @endif
    <tr class="total">
      <td class="label">Total</td>
      <td class="value">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
    </tr>
  </table>

  <div class="footer">
    <p>Thank you for your purchase!</p>
    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
  </div>

</body>

</html>
