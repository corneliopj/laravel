<?php

// Define o caminho para o arquivo de inicialização do Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Cria uma instância do Kernel do console
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "Iniciando a reconstrução do autoloader do Composer e limpeza completa do cache do Laravel...\n\n";

try {
    // PASSO 1: Excluir arquivos de cache do autoloader do Composer diretamente
    echo "Tentando excluir arquivos de cache do autoloader do Composer...\n";
    $composerCacheDir = __DIR__ . '/../vendor/composer/';
    $deletedFiles = 0;
    if (is_dir($composerCacheDir)) {
        $files = glob($composerCacheDir . 'autoload_*.php');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $deletedFiles++;
            }
        }
        if (is_file($composerCacheDir . 'autoload_classmap.php')) {
            unlink($composerCacheDir . 'autoload_classmap.php');
            $deletedFiles++;
        }
        if (is_file($composerCacheDir . 'autoload_namespaces.php')) {
            unlink($composerCacheDir . 'autoload_namespaces.php');
            $deletedFiles++;
        }
        if (is_file($composerCacheDir . 'autoload_psr4.php')) {
            unlink($composerCacheDir . 'autoload_psr4.php');
            $deletedFiles++;
        }
        if (is_file($composerCacheDir . 'autoload_files.php')) {
            unlink($composerCacheDir . 'autoload_files.php');
            $deletedFiles++;
        }
        if (is_file($composerCacheDir . 'autoload_static.php')) {
            unlink($composerCacheDir . 'autoload_static.php');
            $deletedFiles++;
        }
        if (is_file($composerCacheDir . 'installed.php')) {
            unlink($composerCacheDir . 'installed.php');
            $deletedFiles++;
        }
    }
    echo "Excluídos " . $deletedFiles . " arquivos de cache do Composer.\n\n";

    // PASSO 2: Executa o comando Artisan optimize:clear para limpar todos os caches do Laravel
    // Este comando também tenta reconstruir o autoloader para o ambiente de execução do Laravel.
    echo "Executando: php artisan optimize:clear (limpeza completa do cache do Laravel e reconstrução do autoloader)\n";
    $statusOptimizeClear = $kernel->handle(
        $inputOptimize = new Symfony\Component\Console\Input\ArrayInput(['command' => 'optimize:clear']),
        $outputOptimize = new Symfony\Component\Console\Output\BufferedOutput()
    );
    echo $outputOptimize->fetch();
    echo "\nStatus da limpeza completa do cache e autoloader: " . ($statusOptimizeClear === 0 ? "Sucesso" : "Falha") . "\n\n";

} catch (\Exception $e) {
    echo "Ocorreu um erro durante a execução dos comandos: " . $e->getMessage() . "\n\n";
}

echo "Processo concluído. Por favor, tente aceder à sua aplicação novamente.\n\n";
echo "=================================================================================\n";
echo "AVISO DE SEGURANÇA: Exclua este arquivo (composer_autoload_dump.php) do seu servidor IMEDIATAMENTE.\n";
echo "Ele expõe funcionalidades sensíveis da sua aplicação e não deve permanecer em produção.\n";
echo "=================================================================================\n";
echo "</pre>";

// Termina a aplicação
$kernel->terminate($inputOptimize, $outputOptimize);

?>
