<?php

// Define o caminho para o arquivo de inicialização do Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Cria uma instância do Kernel do console
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "Iniciando a limpeza do cache do Laravel...\n\n";

// Array de comandos Artisan para executar
$commands = [
    'cache:clear',   // Limpa a cache da aplicação
    'config:clear',  // Limpa a cache de configuração
    'route:clear',   // Limpa a cache de rotas
    'view:clear',    // Limpa a cache de views
];

foreach ($commands as $command) {
    echo "Executando: php artisan {$command}\n";
    try {
        // Executa o comando Artisan
        $status = $kernel->handle(
            $input = new Symfony\Component\Console\Input\ArrayInput(['command' => $command]),
            $output = new Symfony\Component\Console\Output\BufferedOutput()
        );

        // Pega a saída do comando
        echo $output->fetch();
        echo "Status: " . ($status === 0 ? "Sucesso" : "Falha") . "\n\n";

    } catch (\Exception $e) {
        echo "Erro ao executar o comando {$command}: " . $e->getMessage() . "\n\n";
    }
}

echo "Limpeza do cache concluída.\n\n";
echo "=================================================================================\n";
echo "AVISO DE SEGURANÇA: Exclua este arquivo (clear_cache.php) do seu servidor IMEDIATAMENTE.\n";
echo "Ele expõe funcionalidades sensíveis da sua aplicação e não deve permanecer em produção.\n";
echo "=================================================================================\n";
echo "</pre>";

// Termina a aplicação
$kernel->terminate($input, $output);

?>
