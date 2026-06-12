<?php
/**
 * MIGRATION RUNNER - HERMES AGENT
 * 
 * Este script executa as migrações do Laravel via navegador.
 * IMPORTANTE: Delete este arquivo imediatamente após o uso!
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    echo "<h2>Executando Migrações Específicas...</h2>";
    
    // Lista de migrações novas para forçar a execução
    $migrations = [
        '2026_06_12_132201_create_suinos_table.php',
        '2026_06_12_132918_make_mortes_polymorphic.php',
        '2026_06_12_132932_make_variations_flexible.php',
    ];

    foreach ($migrations as $migration) {
        $path = __DIR__ . '/../database/migrations/' . $migration;
        if (file_exists($path)) {
            echo "Executando: $migration... ";
            // Executa a migração individualmente via Artisan
            $exitCode = $kernel->call('migrate', [
                '--path' => 'database/migrations/' . $migration,
                '--force' => true
            ]);
            
            if ($exitCode === 0) {
                echo "<span style='color: green;'>✅ OK</span><br>";
            } else {
                echo "<span style='color: red;'>❌ FALHOU</span><br>";
            }
        } else {
            echo "Arquivo $migration não encontrado.<br>";
        }
    }

    echo "<p style='font-weight: bold;'>Processo finalizado!</p>";
} catch (\Exception $e) {
    echo "<p style='color: red;'>Erro crítico: " . $e->getMessage() . "</p>";
}

echo "<p><strong>Lembrete:</strong> Delete este arquivo (migrate.php) do servidor agora!</p>";
