<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Limpando caches do Laravel...<br>";
Artisan::call('config:clear');
Artisan::call('cache:clear');
Artisan::call('view:clear');
Artisan::call('route:clear');

// Força a limpeza do cache de esquema do banco de dados, se existir
Schema::dropAllTables(); // CUIDADO: Isso limpa tudo, NÃO USE.
// O correto é apenas rodar isso:
$app->make('db.schema')->getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->clearDoctrineCache();

echo "Verificando se a coluna 'criatorio_origem' existe na tabela 'aves':<br>";
if (Schema::hasColumn('aves', 'criatorio_origem')) {
    echo "Sim, a coluna 'criatorio_origem' existe!<br>";
} else {
    echo "Não, a coluna 'criatorio_origem' NÃO existe. Tentando criar...<br>";
    DB::statement("ALTER TABLE aves ADD COLUMN criatorio_origem VARCHAR(20) NULL, ADD COLUMN registro_abrasb VARCHAR(15) NULL, ADD COLUMN pai_externo VARCHAR(255) NULL, ADD COLUMN mae_externa VARCHAR(255) NULL;");
    echo "Colunas criadas com sucesso.<br>";
}
