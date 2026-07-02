<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "Limpando caches de configuração, rotas, views e dados...<br>";
Artisan::call('config:clear');
Artisan::call('cache:clear');
Artisan::call('view:clear');
Artisan::call('route:clear');

echo "Caches limpos. Por favor, tente acessar o formulário novamente.<br>";
echo "Se o erro persistir, o Opcache do PHP pode estar ativo. Tente reiniciar o serviço PHP no painel de controle.";
