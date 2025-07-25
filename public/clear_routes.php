<?php

// Define o caminho para o arquivo de inicialização do Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Cria uma instância do Kernel do console
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "Iniciando a limpeza do cache de rotas do Laravel...\n\n";

try {
    // Executa o comando Artisan 'route:clear'
    $status = $kernel->handle(
        $input = new Symfony\Component\Console\Input\ArrayInput(['command' => 'route:clear']),
        $output = new Symfony\Component\Console\Output\BufferedOutput()
    );

    // Pega a saída do comando
    echo $output->fetch();
    echo "\nStatus da limpeza de rotas: " . ($status === 0 ? "Sucesso" : "Falha") . "\n\n";

    // Recomenda-se também limpar o cache de configuração e de views para garantir
    echo "Executando: php artisan config:clear\n";
    $statusConfig = $kernel->handle(
        new Symfony\Component\Console\Input\ArrayInput(['command' => 'config:clear']),
        new Symfony\Component\Console\Output\BufferedOutput()
    );
    echo $statusConfig === 0 ? "Sucesso" : "Falha";
    echo "\n\n";

    echo "Executando: php artisan view:clear\n";
    $statusView = $kernel->handle(
        new Symfony\Component\Console\Input\ArrayInput(['command' => 'view:clear']),
        new Symfony\Component\Console\Output\BufferedOutput()
    );
    echo $statusView === 0 ? "Sucesso" : "Falha";
    echo "\n\n";


} catch (\Exception $e) {
    echo "Erro ao executar o comando 'route:clear': " . $e->getMessage() . "\n\n";
}

echo "Limpeza do cache de rotas concluída. Por favor, verifique as rotas novamente.\n\n";
echo "=================================================================================\n";
echo "AVISO DE SEGURANÇA: Exclua este arquivo (clear_routes.php) do seu servidor IMEDIATAMENTE.\n";
echo "Ele expõe funcionalidades sensíveis da sua aplicação e não deve permanecer em produção.\n";
echo "=================================================================================\n";
echo "</pre>";

// Termina a aplicação
$kernel->terminate($input, $output);

?>
