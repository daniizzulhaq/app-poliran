<?php

use Illuminate\Support\Facades\Schedule;

// Jalankan lewat cron cPanel: * * * * * php /path/artisan schedule:run
Schedule::command('lowongan:tutup-kedaluwarsa')->dailyAt('00:05');
