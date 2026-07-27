<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Shipping Label {{ $order->invoice_number }}</title>
  <style>
    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 11px;
      color: #333;
      margin: 0;
      padding: 20px;
    }

    .label-container {
      border: 2px solid #333;
      padding: 20px;
      min-height: 400px;
    }

    .label-header {
      text-align: center;
      border-bottom: 2px dashed #ccc;
      padding-bottom: 12px;
      margin-bottom: 16px;
    }

    .label-header h1 {
      margin: 0;
      font-size: 20px;
      color: #B9A16B;
      letter-spacing: 2px;
    }

    .label-header p {
      margin: 2px 0;
      font-size: 10px;
      color: #666;
    }

    .sections {
      display: flex;
      gap: 20px;
      margin-bottom: 16px;
    }

    .section {
      flex: 1;
    }

    .section-title {
      font-size: 10px;
      color: #B9A16B;
      text-transform: uppercase;
      letter-spacing: 1px;
      border-bottom: 1px solid #B9A16B;
      padding-bottom: 4px;
      margin-bottom: 8px;
    }

    .section-content p {
      margin: 2px 0;
      font-size: 11px;
      line-height: 1.5;
    }

    .section-content .label {
      color: #888;
      font-size: 9px;
    }

    .divider {
      border-top: 1px dashed #ccc;
      margin: 12px 0;
    }

    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }

    .items-table th {
      background: #f5f5f5;
      font-size: 9px;
      padding: 6px 8px;
      text-align: left;
      text-transform: uppercase;
      color: #666;
    }

    .items-table td {
      padding: 6px 8px;
      font-size: 10px;
      border-bottom: 1px solid #eee;
    }

    .items-table td.qty {
      text-align: center;
    }

    .items-table td.price {
      text-align: right;
    }

    .items-table td.subtotal {
      text-align: right;
      font-weight: bold;
    }

    .total-row td {
      border-top: 2px solid #333;
      padding-top: 8px;
      font-weight: bold;
    }

    .total-row td.label-total {
      font-size: 11px;
      text-transform: uppercase;
      color: #333;
    }

    .total-row td.value-total {
      text-align: right;
      font-size: 13px;
      color: #B9A16B;
    }

    .barcode-area {
      margin-top: 16px;
      padding-top: 12px;
      border-top: 2px dashed #ccc;
      text-align: center;
    }

    .barcode-placeholder {
      display: inline-block;
      border: 1px dashed #999;
      padding: 16px 40px;
      font-size: 10px;
      color: #999;
      letter-spacing: 2px;
    }

    .footer-label {
      margin-top: 12px;
      text-align: center;
      font-size: 8px;
      color: #999;
    }
  </style>
</head>

<body>

  <div class="label-container">
    <div class="label-header">
      <h1>SHIPPING LABEL</h1>
      <p>{{ config('app.name') }} — Kp. Cigadog Rt.01 Rw.10 Desa Lingkungpasir Kec. Cibiuk Kab. Garut</p>
      <p>Telp: 085294532451 | Email: fikri.amrulloh15@gmail.com</p>
    </div>

    <div class="sections">
      <div class="section">
        <div class="section-title">Sender</div>
        <div class="section-content">
          <p><strong>{{ config('app.name') }}</strong></p>
          <p>Kp. Cigadog Rt.01 Rw.10</p>
          <p>Desa Lingkungpasir, Kec. Cibiuk</p>
          <p>Kab. Garut</p>
          <p>Telp: 085294532451</p>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Recipient</div>
        <div class="section-content">
          <p><strong>{{ $order->recipient_name }}</strong></p>
          <p>{{ $order->full_address }}</p>
          <p>{{ $order->district }}, {{ $order->city }}</p>
          <p>{{ $order->province }} - {{ $order->postal_code }}</p>
          <p>Telp: {{ $order->phone }}</p>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Order Info</div>
        <div class="section-content">
          <p><span class="label">Invoice:</span> {{ $order->invoice_number }}</p>
          <p><span class="label">Date:</span> {{ $order->created_at->format('d/m/Y') }}</p>
          <p><span class="label">Payment:</span> {{ ucfirst($order->payment_method) }}</p>
          <p><span class="label">Status:</span> {{ ucfirst($order->status) }}</p>
        </div>
      </div>
    </div>

    <div class="divider"></div>

    <table class="items-table">
      <thead>
        <tr>
          <th style="width:40%">Product</th>
          <th style="width:20%" class="price">Price</th>
          <th style="width:15%" class="qty">Qty</th>
          <th style="width:25%" class="price">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($order->items as $item)
          <tr>
            <td>{{ $item->product_name }}</td>
            <td class="price">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="qty">{{ $item->quantity }}</td>
            <td class="subtotal">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
          </tr>
        @endforeach
        <tr class="total-row">
          <td colspan="3" class="label-total">Total</td>
          <td class="value-total">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>

    <div class="barcode-area">
      <div class="barcode-placeholder">[ TRACKING BARCODE ]</div>
    </div>

    <div class="footer-label">
      <p>This label is computer generated. No signature required.</p>
    </div>
  </div>

</body>

</html>