<?php
require __DIR__ . '/../vendor/autoload.php';
 = require_once __DIR__ . '/../bootstrap/app.php';
 = ->make(Illuminate\Contracts\Console\Kernel::class);

echo "Limpando cache de config...<br>";
\Illuminate\Support\Facades\Artisan::call('config:clear');
\Illuminate\Support\Facades\Artisan::call('cache:clear');
\Illuminate\Support\Facades\Artisan::call('view:clear');

echo "Verificando colunas na tabela 'aves':<br>";
$columns = \Illuminate\Support\Facades\DB::select('DESCRIBE aves');
foreach ($columns as $column) {
    echo $column->Field . "<br>";
}
