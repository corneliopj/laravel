<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AveController;
use App\Http\Controllers\TipoAveController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\IncubacaoController;
use App\Http\Controllers\VariacaoController;
use App\Http\Controllers\AcasalamentoController;
use App\Http\Controllers\PosturaOvoController;
use App\Http\Controllers\MorteController; // NOVO: Importa o MorteController
// Importa os controladores financeiros
use App\Http\Controllers\Financeiro\CategoriaController;
use App\Http\Controllers\Financeiro\ReceitaController;
use App\Http\Controllers\Financeiro\DespesaController;
use App\Http\Controllers\Financeiro\FinanceiroDashboardController;
use App\Http\Controllers\Financeiro\TransacaoRecorrenteController;
use App\Http\Controllers\Financeiro\VendaController;
use App\Http\Controllers\Financeiro\ReservaController;
use App\Http\Controllers\Financeiro\ContrachequeController;
use App\Http\Controllers\PlantelController;
use App\Http\Controllers\MovimentacaoPlantelController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Rota Pública para Validação de Certidão (ESTÁTICA, ANTES DA DINÂMICA)
Route::get('certidao/validar', [AveController::class, 'showValidarForm'])->name('certidao.validar');
Route::post('certidao/validar', [AveController::class, 'processValidarForm'])->name('certidao.validar.post');

// Rota Pública para a Certidão (DINÂMICA, DEPOIS DA ESTÁTICA)
Route::get('certidao/{validation_code}', [AveController::class, 'showCertidao'])->name('certidao.show');

// Rotas de Autenticação (não protegidas pelo middleware 'auth' inicialmente)
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');


// Rotas de Registro (apenas para administradores, a verificação está no controlador)
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

// Grupo de Rotas Protegidas (apenas para utilizadores autenticados)
Route::middleware(['auth'])->group(function () {
    // Rota do Dashboard (Home)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas para Aves (personalizadas, não resource puro devido a ações específicas)
    Route::get('aves', [AveController::class, 'index'])->name('aves.index');
    Route::get('aves/criar', [AveController::class, 'create'])->name('aves.create');
    Route::post('aves', [AveController::class, 'store'])->name('aves.store');
    Route::get('aves/search-suggestions', [AveController::class, 'searchSuggestions'])->name('aves.searchSuggestions');
    Route::get('aves/search', [AveController::class, 'search'])->name('aves.search');
    Route::get('aves/{ave}', [AveController::class, 'show'])->name('aves.show');
    Route::get('aves/{ave}/editar', [AveController::class, 'edit'])->name('aves.edit');
    Route::put('aves/{ave}', [AveController::class, 'update'])->name('aves.update');
    Route::delete('aves/{ave}', [AveController::class, 'destroy'])->name('aves.destroy');
    Route::post('aves/{ave}/restore', [AveController::class, 'restore'])->name('aves.restore');
    Route::delete('aves/{ave}/force-delete', [AveController::class, 'forceDelete'])->name('aves.forceDelete');
    Route::post('aves/{ave}/expedir-certidao', [AveController::class, 'expedirCertidao'])->name('aves.expedirCertidao');

    // NOVO: Rota para registrar morte de uma ave específica
    // Isso redirecionará para o formulário de criação de morte, pré-preenchendo a ave.
    Route::get('aves/{ave}/registrar-morte', [MorteController::class, 'create'])->name('aves.registerDeath');


    // Rotas Explícitas para Incubacoes (para garantir o nome do parâmetro)
    Route::get('incubacoes', [IncubacaoController::class, 'index'])->name('incubacoes.index');
    Route::get('incubacoes/criar', [IncubacaoController::class, 'create'])->name('incubacoes.create');
    Route::post('incubacoes', [IncubacaoController::class, 'store'])->name('incubacoes.store');
    Route::get('incubacoes/{incubacao}', [IncubacaoController::class, 'show'])->name('incubacoes.show');
    Route::get('incubacoes/{incubacao}/editar', [IncubacaoController::class, 'edit'])->name('incubacoes.edit');
    Route::put('incubacoes/{incubacao}', [IncubacaoController::class, 'update'])->name('incubacoes.update');
    Route::delete('incubacoes/{incubacao}', [IncubacaoController::class, 'destroy'])->name('incubacoes.destroy');
    // Rota para a ficha de incubação
    Route::get('incubacoes/{incubacao}/ficha', [IncubacaoController::class, 'ficha'])->name('incubacoes.ficha');


    // Rotas de Recurso para Outros Modelos (mantidos como resource)
    Route::resource('tipos_aves', TipoAveController::class);
    Route::resource('variacoes', VariacaoController::class);
    Route::resource('lotes', LoteController::class);

    Route::resource('acasalamentos', AcasalamentoController::class);
    Route::resource('posturas_ovos', PosturaOvoController::class);

    // Rotas adicionais para Postura de Ovos
    Route::post('posturas_ovos/{postura_ovo}/incrementar-ovos', [PosturaOvoController::class, 'incrementOvos'])->name('posturas_ovos.incrementOvos');
    Route::post('posturas_ovos/{postura_ovo}/encerrar', [PosturaOvoController::class, 'encerrarPostura'])->name('posturas_ovos.encerrar');

    // Rotas para o Módulo Financeiro
    Route::prefix('financeiro')->name('financeiro.')->group(function () {
        // Dashboard Financeiro
        Route::get('dashboard', [FinanceiroDashboardController::class, 'index'])->name('dashboard');

        // CRUD de Categorias
        Route::resource('categorias', CategoriaController::class);

        // CRUD de Receitas
        Route::resource('receitas', ReceitaController::class);

        // CRUD de Despesas
        Route::resource('despesas', DespesaController::class);

        // Rotas de Transações Recorrentes
        Route::resource('transacoes_recorrentes', TransacaoRecorrenteController::class);

        // Rotas de Vendas (PDV)
        Route::get('vendas/search-aves-for-sale', [VendaController::class, 'searchAvesForSale'])->name('vendas.searchAvesForSale');
        Route::resource('vendas', VendaController::class);

        // Rotas de Reservas (Pedidos/Orçamento) (NOVO)
        Route::get('reservas/search-aves-for-reserva', [ReservaController::class, 'searchAvesForReserva'])->name('reservas.searchAvesForReserva');
        Route::post('reservas/{reserva}/converter-venda', [ReservaController::class, 'convertToVenda'])->name('reservas.convertToVenda');
        Route::resource('reservas', ReservaController::class);


        // Rotas de Relatórios Financeiros
        Route::get('relatorios', [FinanceiroDashboardController::class, 'relatoriosIndex'])->name('relatorios.index');
        Route::get('relatorios/transacoes', [FinanceiroDashboardController::class, 'transacoes'])->name('relatorios.transacoes');
        Route::get('relatorios/fluxo-caixa', [FinanceiroDashboardController::class, 'fluxoCaixa'])->name('relatorios.fluxo_caixa');
        Route::get('relatorios/por-categoria', [FinanceiroDashboardController::class, 'relatorioPorCategoria'])->name('relatorios.por_categoria');

        // NOVO: Rotas para o Contracheque
        Route::get('contracheque', [ContrachequeController::class, 'index'])->name('contracheque.index');
        Route::post('contracheque', [ContrachequeController::class, 'store'])->name('contracheque.store');
        Route::delete('contracheque/{contracheque}', [ContrachequeController::class, 'destroy'])->name('contracheque.destroy');
    });

    // Rotas para o Módulo Plantel
    Route::resource('plantel', PlantelController::class);

    // Rotas para o Módulo MovimentacoesPlantel
    Route::resource('movimentacoes-plantel', MovimentacaoPlantelController::class);
    Route::get('plantel/{plantel}/movimentacoes/create', [MovimentacaoPlantelController::class, 'create'])->name('plantel.movimentacoes.create');

    // NOVO: Rotas para o Módulo Mortes (Centralizado)
    Route::resource('mortes', MorteController::class);
});
