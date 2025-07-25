<?php

// Define o caminho para o arquivo de inicialização do Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Cria uma instância do Kernel do console
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "Listando rotas do Laravel...\n\n";

try {
    // Executa o comando Artisan 'route:list'
    $status = $kernel->handle(
        $input = new Symfony\Component\Console\Input\ArrayInput(['command' => 'route:list']),
        $output = new Symfony\Component\Console\Output\BufferedOutput()
    );

    // Pega a saída do comando
    echo $output->fetch();
    echo "\nStatus da listagem de rotas: " . ($status === 0 ? "Sucesso" : "Falha") . "\n\n";

} catch (\Exception $e) {
    echo "Erro ao executar o comando 'route:list': " . $e->getMessage() . "\n\n";
}

echo "=================================================================================\n";
echo "AVISO DE SEGURANÇA: Exclua este arquivo (route_debugger.php) do seu servidor IMEDIATAMENTE.\n";
echo "Ele expõe funcionalidades sensíveis da sua aplicação e não deve permanecer em produção.\n";
echo "=================================================================================\n";
echo "</pre>";

// Termina a aplicação
$kernel->terminate($input, $output);

?>
