<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;
use App\Models\Receita;
use App\Models\Despesa;
use Carbon\Carbon;

try {
    $nome = 'Wellington';
    $user = User::where('name', 'like', '%' . $nome . '%')->first();
    if (!$user) {
        die("Funcionário $nome não encontrado no banco de dados.");
    }

    $inicioMes = Carbon::now()->startOfMonth();
    $fimMes = Carbon::now()->endOfMonth();

    $receitas = Receita::where('user_id', $user->id)->whereBetween('data', [$inicioMes, $fimMes])->sum('valor');
    $despesas = Despesa::where('user_id', $user->id)->whereBetween('data', [$inicioMes, $fimMes])->sum('valor');

    echo "<h1>Saldo de {$user->name}</h1>";
    echo "Período: " . $inicioMes->format('M/Y') . "<br>";
    echo "Receitas: R$ " . number_format($receitas, 2, ',', '.') . "<br>";
    echo "Despesas: R$ " . number_format($despesas, 2, ',', '.') . "<br>";
    echo "<strong>Saldo Líquido: R$ " . number_format($receitas - $despesas, 2, ',', '.') . "</strong>";
} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage();
}
