<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan reservasi "Bayar di Restoran" yang melewati tenggat (mis. 2 jam)
Schedule::command('payments:expire')->everyMinute()->withoutOverlapping();