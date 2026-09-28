<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->removeDuplicatePayments();

        Schema::table('payments', function (Blueprint $table) {
            $table->unique(['order_id', 'transaction_id', 'transaction_status']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['order_id', 'transaction_id', 'transaction_status']);
        });
    }

    /**
     * Bersihkan baris duplikat lama (hasil webhook/sync berulang sebelum ada index unik),
     * sisakan id terkecil per kelompok.
     */
    private function removeDuplicatePayments(): void
    {
        $duplicates = DB::table('payments')
            ->select('order_id', 'transaction_id', 'transaction_status', DB::raw('MIN(id) as keep_id'))
            ->groupBy('order_id', 'transaction_id', 'transaction_status')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $query = DB::table('payments')
                ->where('order_id', $duplicate->order_id)
                ->where('transaction_status', $duplicate->transaction_status)
                ->where('id', '>', $duplicate->keep_id);

            if ($duplicate->transaction_id === null) {
                $query->whereNull('transaction_id');
            } else {
                $query->where('transaction_id', $duplicate->transaction_id);
            }

            $query->delete();
        }
    }
};
