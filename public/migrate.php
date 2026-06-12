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
    echo "<h2>Executando Migrações do Banco de Dados...</h2>";
    
    // Simula a execução do comando 'php artisan migrate'
    $exitCode = $kernel->call('migrate', ['--force' => true]);
    
    if ($exitCode === 0) {
        echo "<p style='color: green; font-weight: bold;'>✅ Migrações executadas com sucesso!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Ocorreu um erro durante a execução das migrações.</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color: red;'>Erro crítico: " . $e->getMessage() . "</p>";
}

echo "<p><strong>Lembrete:</strong> Delete este arquivo (migrate.php) do servidor agora!</p>";
