<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

echo "Limpando cache...<br>";
Artisan::call('config:clear');
Artisan::call('cache:clear');
Artisan::call('view:clear');

echo "Verificando colunas na tabela 'aves':<br>";
$columns = DB::select('DESCRIBE aves');
foreach ($columns as $column) {
    echo $column->Field . "<br>";
}
