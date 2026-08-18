<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class ExpirePendingPayments extends Command
{
    protected $signature = 'payments:expire';

    protected $description = 'Batalkan reservasi "Bayar di Restoran" yang melewati tenggat pembayaran (tidak membayar di kasir).';

    public function handle(): int
    {
        $expired = Payment::query()
            ->where('method', 'cash')
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->whereHas('reservation', function (Builder $query): void {
                $query->whereIn('status', ['pending', 'confirmed']);
            })
            ->with('reservation')
            ->get();

        $count = 0;
        foreach ($expired as $payment) {
            $reservation = $payment->reservation;

            $payment->update(['status' => 'failed']);

            if ($reservation) {
                $reservation->update([
                    'status' => 'cancelled',
                    'payment_status' => 'unpaid',
                ]);

                $table = $reservation->table;
                if ($table && $table->status === 'reserved') {
                    $table->update(['status' => 'available']);
                }
            }

            $count++;
        }

        $this->info("{$count} pending payment(s) expired.");

        return self::SUCCESS;
    }
}