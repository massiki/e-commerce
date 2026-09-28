<?php

return [
    'merchant_id' => env('MERCHANT_ID'),
    'client_key' => env('CLIENT_KEY'),
    'server_key' => env('SERVER_KEY'),

    // URL script Snap sesuai environment (prod = app.snap.midtrans.com).
    // Catatan: config file tidak boleh pakai app()->environment() — 'env' belum terikat saat config dibaca.
    'snap_js' => env('APP_ENV', 'production') === 'production'
        ? 'https://app.snap.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',

    // Auto-expire order Midtrans yang belum bayar (menit).
    'expire_unpaid_minutes' => (int) env('MIDTRANS_EXPIRE_MINUTES', 10),
];
