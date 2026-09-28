<?php

namespace App\Services;

use App\Exceptions\MidtransUnavailableException;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = (string) config('midtrans.server_key');
        Config::$isProduction = app()->environment('production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken(array $payload): string
    {
        return Snap::getSnapToken($payload);
    }

    /**
     * Ambil status transaksi dari Midtrans.
     *
     * @return object|null null jika transaksi tidak ditemukan (404)
     *
     * @throws MidtransUnavailableException jika API tidak bisa dijangkau
     */
    public function getStatus(string $orderId): ?object
    {
        try {
            $status = Transaction::status($orderId);
        } catch (\Exception $e) {
            if ((int) $e->getCode() === 404) {
                return null;
            }

            throw new MidtransUnavailableException($e->getMessage(), (int) $e->getCode(), $e);
        }

        return $status;
    }

    /**
     * Batalkan transaksi di Midtrans sebelum settlement.
     * true = aman untuk melanjutkan pembatalan lokal (termasuk jika memang tidak ada transaksi).
     */
    public function cancel(string $orderId): bool
    {
        try {
            Transaction::cancel($orderId);

            return true;
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            // Tidak ada transaksi aktif — tidak ada yang perlu dibatalkan di Midtrans.
            if ($code === 404) {
                return true;
            }

            // 406 = transaksi sudah tidak bisa dibatalkan (sudah/settlement/dsb).
            if ($code === 406) {
                return false;
            }

            Log::warning('Midtrans cancel failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Expire transaksi yang masih pending di Midtrans.
     *
     * @return object|null status terbaru setelah expire, null jika gagal
     *
     * @throws MidtransUnavailableException jika API tidak bisa dijangkau
     */
    public function expire(string $orderId): ?object
    {
        try {
            return Transaction::expire($orderId);
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            // Sudah tidak pending (406) atau tidak ditemukan (404) — gunakan status final.
            if ($code === 406 || $code === 404) {
                return $this->getStatus($orderId);
            }

            throw new MidtransUnavailableException($e->getMessage(), $code, $e);
        }
    }
}
