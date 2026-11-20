<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\DB::table('users')
    ->where(function($q){
        $q->whereNull('status')->orWhere('status', '');
    })
    ->update([
        'status' => 'approved',
        'approved_at' => now(),
    ]);

echo "done";
