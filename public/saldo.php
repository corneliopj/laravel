<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Receita;
use App\Models\Despesa;
use Carbon\Carbon;

$nome = 'Wellington';
$user = User::where('name', 'like', '%' . $nome . '%')->first();

if (!$user) {
    die("Funcionário $nome não encontrado.");
}

$inicioMes = Carbon::now()->startOfMonth();
$fimMes = Carbon::now()->endOfMonth();

$receitas = Receita::where('user_id', $user->id)
                    ->whereBetween('data', [$inicioMes, $fimMes])
                    ->sum('valor');

$despesas = Despesa::where('user_id', $user->id)
                    ->whereBetween('data', [$inicioMes, $fimMes])
                    ->sum('valor');

echo "Funcionário: " . $user->name . "\n";
echo "Período: " . $inicioMes->format('M/Y') . "\n";
echo "Receitas: R$ " . number_format($receitas, 2, ',', '.') . "\n";
echo "Despesas: R$ " . number_format($despesas, 2, ',', '.') . "\n";
echo "Saldo Líquido: R$ " . number_format($receitas - $despesas, 2, ',', '.') . "\n";
