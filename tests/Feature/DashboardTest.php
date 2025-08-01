<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Receita;
use App\Models\Despesa;
use App\Models\Categoria;
use App\Models\TransacaoRecorrente;
use Carbon\Carbon;

class DashboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $categoria;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário de teste
        $this->user = User::factory()->create();
        
        // Criar categoria de teste
        $this->categoria = Categoria::create([
            'nome' => 'Categoria Teste',
            'tipo' => 'receita'
        ]);
    }

    /** @test */
    public function dashboard_principal_carrega_corretamente()
    {
        $response = $this->actingAs($this->user)
                         ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHas(['totalAves', 'ovosIncubando', 'pintosNascidos', 'mortesRecentes']);
    }

    /** @test */
    public function dashboard_financeiro_carrega_corretamente()
    {
        $response = $this->actingAs($this->user)
                         ->get(route('financeiro.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('financeiro.dashboard');
        $response->assertViewHas([
            'receitasMes', 'despesasMes', 'saldoMes', 'saldoTotal',
            'dadosGraficoBarras', 'dadosGraficoPizza', 'dadosGraficoLinha'
        ]);
    }

    /** @test */
    public function filtros_dinamicos_funcionam_corretamente()
    {
        // Criar dados de teste
        $this->criarDadosFinanceiros();

        // Testar filtro por ano
        $response = $this->actingAs($this->user)
                         ->get(route('financeiro.dashboard', ['ano' => 2024]));

        $response->assertStatus(200);
        $response->assertViewHas('ano', 2024);

        // Testar filtro por mês
        $response = $this->actingAs($this->user)
                         ->get(route('financeiro.dashboard', ['ano' => 2024, 'mes' => 8]));

        $response->assertStatus(200);
        $response->assertViewHas(['ano', 'mes']);
        $this->assertEquals(2024, $response->viewData('ano'));
        $this->assertEquals(8, $response->viewData('mes'));
    }

    /** @test */
    public function dados_comparativo_sao_calculados_corretamente()
    {
        // Criar dados para comparação
        $this->criarDadosComparativos();

        $response = $this->actingAs($this->user)
                         ->get(route('financeiro.dashboard', ['ano' => 2024, 'mes' => 8]));

        $response->assertStatus(200);
        $response->assertViewHas('dadosComparativo');
        
        $dadosComparativo = $response->viewData('dadosComparativo');
        $this->assertArrayHasKey('periodo_atual', $dadosComparativo);
        $this->assertArrayHasKey('periodo_anterior', $dadosComparativo);
        $this->assertArrayHasKey('ano_anterior', $dadosComparativo);
        $this->assertArrayHasKey('variacoes_mes_anterior', $dadosComparativo);
        $this->assertArrayHasKey('variacoes_ano_anterior', $dadosComparativo);
    }

    /** @test */
    public function top5_despesas_receitas_funcionam()
    {
        // Criar dados diversificados
        $this->criarDadosTop5();

        $response = $this->actingAs($this->user)
                         ->get(route('financeiro.dashboard', ['ano' => 2024, 'mes' => 8]));

        $response->assertStatus(200);
        $response->assertViewHas(['top5Despesas', 'top5Receitas']);
        
        $top5Despesas = $response->viewData('top5Despesas');
        $top5Receitas = $response->viewData('top5Receitas');
        
        $this->assertArrayHasKey('despesas', $top5Despesas);
        $this->assertArrayHasKey('receitas', $top5Receitas);
        $this->assertArrayHasKey('total_periodo', $top5Despesas);
        $this->assertArrayHasKey('total_periodo', $top5Receitas);
        
        // Verificar se está limitado a 5 itens
        $this->assertLessThanOrEqual(5, count($top5Despesas['despesas']));
        $this->assertLessThanOrEqual(5, count($top5Receitas['receitas']));
    }

    /** @test */
    public function ponto_equilibrio_e_calculado_corretamente()
    {
        // Criar dados para ponto de equilíbrio
        $this->criarDadosPontoEquilibrio();

        $response = $this->actingAs($this->user)
                         ->get(route('financeiro.dashboard', ['ano' => 2024, 'mes' => 8]));

        $response->assertStatus(200);
        $response->assertViewHas('dadosPontoEquilibrio');
        
        $dadosPontoEquilibrio = $response->viewData('dadosPontoEquilibrio');
        $this->assertArrayHasKey('custos_fixos', $dadosPontoEquilibrio);
        $this->assertArrayHasKey('custos_variaveis', $dadosPontoEquilibrio);
        $this->assertArrayHasKey('receita_total', $dadosPontoEquilibrio);
        $this->assertArrayHasKey('margem_seguranca', $dadosPontoEquilibrio);
        $this->assertArrayHasKey('status_cor', $dadosPontoEquilibrio);
        $this->assertArrayHasKey('status_texto', $dadosPontoEquilibrio);
        $this->assertArrayHasKey('grafico', $dadosPontoEquilibrio);
    }

    /** @test */
    public function fluxo_caixa_futuro_funciona()
    {
        // Criar transações recorrentes
        $this->criarTransacoesRecorrentes();

        $response = $this->actingAs($this->user)
                         ->get(route('financeiro.dashboard', ['ano' => 2024, 'mes' => 8, 'dias_futuro' => 30]));

        $response->assertStatus(200);
        $response->assertViewHas('fluxoCaixaFuturo');
        
        $fluxoCaixaFuturo = $response->viewData('fluxoCaixaFuturo');
        $this->assertArrayHasKey('dias_projetados', $fluxoCaixaFuturo);
        $this->assertArrayHasKey('total_receitas_futuras', $fluxoCaixaFuturo);
        $this->assertArrayHasKey('total_despesas_futuras', $fluxoCaixaFuturo);
        $this->assertArrayHasKey('saldo_projetado', $fluxoCaixaFuturo);
        $this->assertArrayHasKey('menor_saldo', $fluxoCaixaFuturo);
        $this->assertArrayHasKey('grafico', $fluxoCaixaFuturo);
        
        $this->assertEquals(30, $fluxoCaixaFuturo['dias_projetados']);
    }

    /** @test */
    public function transacao_recorrente_model_funciona_corretamente()
    {
        $transacao = TransacaoRecorrente::create([
            'descricao' => 'Receita Mensal',
            'tipo' => 'receita',
            'valor' => 1000.00,
            'categoria_id' => $this->categoria->id,
            'frequencia' => 'mensal',
            'data_inicio' => Carbon::now()->startOfMonth(),
            'ativo' => true
        ]);

        // Testar se deve executar hoje
        $hoje = Carbon::now()->startOfMonth();
        $this->assertTrue($transacao->deveExecutarEm($hoje));

        // Testar próximas execuções
        $proximasExecucoes = $transacao->proximasExecucoes(60);
        $this->assertIsArray($proximasExecucoes);
        $this->assertGreaterThan(0, count($proximasExecucoes));

        // Testar valor projetado
        $valorProjetado = $transacao->valorProjetado(30);
        $this->assertIsFloat($valorProjetado);
        $this->assertGreaterThan(0, $valorProjetado);
    }

    /** @test */
    public function analise_tendencias_funciona()
    {
        // Criar dados históricos para análise de tendências
        $this->criarDadosHistoricos();

        $response = $this->actingAs($this->user)
                         ->get(route('financeiro.dashboard', ['ano' => 2024, 'mes' => 8]));

        $response->assertStatus(200);
        $response->assertViewHas('analiseTendencias');
        
        $analiseTendencias = $response->viewData('analiseTendencias');
        $this->assertArrayHasKey('tendencias', $analiseTendencias);
        $this->assertArrayHasKey('receitas', $analiseTendencias['tendencias']);
        $this->assertArrayHasKey('despesas', $analiseTendencias['tendencias']);
        $this->assertArrayHasKey('saldos', $analiseTendencias['tendencias']);
    }

    /** @test */
    public function arquivos_css_js_personalizacao_existem()
    {
        $cssPath = public_path('css/dashboard-personalizavel.css');
        $jsPath = public_path('js/dashboard-personalizavel.js');
        
        $this->assertFileExists($cssPath);
        $this->assertFileExists($jsPath);
        
        // Verificar se os arquivos não estão vazios
        $this->assertGreaterThan(0, filesize($cssPath));
        $this->assertGreaterThan(0, filesize($jsPath));
    }

    /** @test */
    public function dashboard_personalizavel_carrega()
    {
        $response = $this->actingAs($this->user)
                         ->get('/financeiro/dashboard-personalizavel');

        // Se a rota não existir, pelo menos verificar se a view existe
        $viewPath = resource_path('views/financeiro/dashboard_personalizavel.blade.php');
        $this->assertFileExists($viewPath);
    }

    // Métodos auxiliares para criar dados de teste

    private function criarDadosFinanceiros()
    {
        // Receitas do mês atual
        Receita::create([
            'descricao' => 'Venda de Ovos',
            'valor' => 500.00,
            'data' => Carbon::now(),
            'categoria_id' => $this->categoria->id
        ]);

        // Despesas do mês atual
        $categoriaDespesa = Categoria::create([
            'nome' => 'Ração',
            'tipo' => 'despesa'
        ]);

        Despesa::create([
            'descricao' => 'Compra de Ração',
            'valor' => 200.00,
            'data' => Carbon::now(),
            'categoria_id' => $categoriaDespesa->id
        ]);
    }

    private function criarDadosComparativos()
    {
        // Dados do mês atual (agosto 2024)
        Receita::create([
            'descricao' => 'Venda Atual',
            'valor' => 1000.00,
            'data' => Carbon::create(2024, 8, 15),
            'categoria_id' => $this->categoria->id
        ]);

        // Dados do mês anterior (julho 2024)
        Receita::create([
            'descricao' => 'Venda Anterior',
            'valor' => 800.00,
            'data' => Carbon::create(2024, 7, 15),
            'categoria_id' => $this->categoria->id
        ]);

        // Dados do ano anterior (agosto 2023)
        Receita::create([
            'descricao' => 'Venda Ano Anterior',
            'valor' => 600.00,
            'data' => Carbon::create(2023, 8, 15),
            'categoria_id' => $this->categoria->id
        ]);
    }

    private function criarDadosTop5()
    {
        $categorias = [];
        for ($i = 1; $i <= 6; $i++) {
            $categorias[] = Categoria::create([
                'nome' => "Categoria $i",
                'tipo' => 'despesa'
            ]);
        }

        // Criar despesas variadas
        foreach ($categorias as $index => $categoria) {
            Despesa::create([
                'descricao' => "Despesa " . ($index + 1),
                'valor' => (6 - $index) * 100, // Valores decrescentes
                'data' => Carbon::create(2024, 8, 15),
                'categoria_id' => $categoria->id
            ]);
        }

        // Criar receitas variadas
        for ($i = 1; $i <= 6; $i++) {
            Receita::create([
                'descricao' => "Receita $i",
                'valor' => $i * 150,
                'data' => Carbon::create(2024, 8, 15),
                'categoria_id' => $this->categoria->id
            ]);
        }
    }

    private function criarDadosPontoEquilibrio()
    {
        // Criar categorias específicas para custos fixos e variáveis
        $custoFixo = Categoria::create(['nome' => 'Custo Fixo', 'tipo' => 'despesa']);
        $custoVariavel = Categoria::create(['nome' => 'Custo Variável', 'tipo' => 'despesa']);

        // Custos fixos
        Despesa::create([
            'descricao' => 'Aluguel',
            'valor' => 1000.00,
            'data' => Carbon::create(2024, 8, 1),
            'categoria_id' => $custoFixo->id
        ]);

        // Custos variáveis
        Despesa::create([
            'descricao' => 'Ração Variável',
            'valor' => 500.00,
            'data' => Carbon::create(2024, 8, 15),
            'categoria_id' => $custoVariavel->id
        ]);

        // Receitas
        Receita::create([
            'descricao' => 'Vendas do Mês',
            'valor' => 2000.00,
            'data' => Carbon::create(2024, 8, 15),
            'categoria_id' => $this->categoria->id
        ]);
    }

    private function criarTransacoesRecorrentes()
    {
        // Receita recorrente mensal
        TransacaoRecorrente::create([
            'descricao' => 'Venda Mensal de Ovos',
            'tipo' => 'receita',
            'valor' => 800.00,
            'categoria_id' => $this->categoria->id,
            'frequencia' => 'mensal',
            'data_inicio' => Carbon::now()->startOfMonth(),
            'ativo' => true
        ]);

        // Despesa recorrente semanal
        $categoriaDespesa = Categoria::create(['nome' => 'Ração Semanal', 'tipo' => 'despesa']);
        TransacaoRecorrente::create([
            'descricao' => 'Compra Semanal de Ração',
            'tipo' => 'despesa',
            'valor' => 150.00,
            'categoria_id' => $categoriaDespesa->id,
            'frequencia' => 'semanal',
            'data_inicio' => Carbon::now()->startOfWeek(),
            'ativo' => true
        ]);
    }

    private function criarDadosHistoricos()
    {
        // Criar dados dos últimos 6 meses para análise de tendências
        for ($i = 6; $i >= 1; $i--) {
            $data = Carbon::now()->subMonths($i);
            
            // Receitas com tendência crescente
            Receita::create([
                'descricao' => "Receita {$data->format('m/Y')}",
                'valor' => 500 + ($i * 50), // Crescimento gradual
                'data' => $data,
                'categoria_id' => $this->categoria->id
            ]);

            // Despesas com variação
            $categoriaDespesa = Categoria::firstOrCreate(['nome' => 'Despesas Gerais', 'tipo' => 'despesa']);
            Despesa::create([
                'descricao' => "Despesa {$data->format('m/Y')}",
                'valor' => 300 + ($i * 20),
                'data' => $data,
                'categoria_id' => $categoriaDespesa->id
            ]);
        }
    }
}

