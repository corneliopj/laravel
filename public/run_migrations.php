<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "Executando migrações...<br>";
$exitCode = Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();
echo "<br>Migrações finalizadas com código de saída: " . $exitCode;
