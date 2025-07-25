<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Contracheque;
use App\Models\Venda; // Importa o modelo de Venda para comissões
// REMOVIDO: use App\Models\Financeiro\Despesa; // Não é mais necessário para buscar salário aqui
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContrachequeController extends Controller
{
    /**
     * Exibe o contracheque do usuário logado para o mês e ano especificados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $mes = $request->input('mes', Carbon::now()->month);
        $ano = $request->input('ano', Carbon::now()->year);
        $userId = Auth::id();

        // 1. Calcular Comissões do Mês (mantido, pois vem de Vendas)
        $comissaoAcumuladaMes = 0;
        if (Auth::check()) {
            $dataInicioMes = Carbon::createFromDate($ano, $mes, 1)->startOfDay();
            $dataFimMes = Carbon::createFromDate($ano, $mes)->endOfMonth()->endOfDay();

            $comissaoAcumuladaMes = Venda::where('user_id', $userId)
                ->where('comissao_paga', true)
                ->whereBetween('data_venda', [$dataInicioMes, $dataFimMes])
                ->with('despesaComissao')
                ->get()
                ->sum(function ($venda) {
                    return $venda->despesaComissao ? $venda->despesaComissao->valor : 0;
                });
        }

        // 2. Buscar TODOS os Lançamentos de Contracheque para o usuário e mês/ano
        $contrachequeLancamentos = Contracheque::where('user_id', $userId)
            ->whereMonth('data', $mes)
            ->whereYear('data', $ano)
            ->get();

        // 3. Inicializa o sumário do contracheque
        $contrachequeSumario = [
            'salario' => 0,
            'comissoes' => $comissaoAcumuladaMes, // Comissões vêm da lógica acima
            'adiantamento' => 0,
            'cartao_credito' => 0,
            'outros_positivos' => 0,
            'outros_negativos' => 0,
            'valor_bruto' => 0,
            'descontos' => 0,
            'saldo_liquido' => 0,
            'lancamentos_detalhados' => []
        ];

        // 4. Processa cada lançamento do modelo Contracheque para calcular os totais e detalhar
        // O salário agora será um lançamento 'positivo' na tabela 'contracheques'
        foreach ($contrachequeLancamentos as $lancamento) {
            $lancamentoDetalhado = [
                'id' => $lancamento->id,
                'descricao' => $lancamento->descricao,
                'valor' => $lancamento->valor,
                'tipo_lancamento' => $lancamento->tipo_lancamento,
                'data' => $lancamento->data->format('d/m/Y')
            ];

            if ($lancamento->tipo_lancamento == 'positivo') {
                // Identifica 'salário' especificamente (case-insensitive)
                if (mb_strtolower($lancamento->descricao) == 'salário' || mb_strtolower($lancamento->descricao) == 'salario') {
                    $contrachequeSumario['salario'] += $lancamento->valor;
                } else {
                    $contrachequeSumario['outros_positivos'] += $lancamento->valor;
                }
            } else { // 'negativo'
                $contrachequeSumario['descontos'] += $lancamento->valor; // Descontos são somados aqui
                // Identifica 'adiantamento' e 'cartão de crédito' especificamente (case-insensitive)
                if (mb_strtolower($lancamento->descricao) == 'adiantamento') {
                    $contrachequeSumario['adiantamento'] += $lancamento->valor;
                } elseif (mb_strtolower($lancamento->descricao) == 'cartão de crédito' || mb_strtolower($lancamento->descricao) == 'cartao de credito') {
                    $contrachequeSumario['cartao_credito'] += $lancamento->valor;
                } else {
                    $contrachequeSumario['outros_negativos'] += $lancamento->valor;
                }
            }
            $contrachequeSumario['lancamentos_detalhados'][] = $lancamentoDetalhado;
        }

        // 5. Calcular Valor Bruto, Descontos e Saldo Líquido Finais
        // Valor bruto inclui salário (do contracheque), comissões (de Venda) e outros positivos (do contracheque)
        $contrachequeSumario['valor_bruto'] = $contrachequeSumario['salario'] + $contrachequeSumario['comissoes'] + $contrachequeSumario['outros_positivos'];
        $contrachequeSumario['saldo_liquido'] = $contrachequeSumario['valor_bruto'] - $contrachequeSumario['descontos'];


        return view('financeiro.contracheque.index', compact(
            'contrachequeSumario',
            'mes',
            'ano'
        ));
    }

    /**
     * Armazena um novo lançamento de contracheque.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'tipo_lancamento' => 'required|in:positivo,negativo',
            'data' => 'required|date',
            'user_id' => 'required|exists:users,id', // Garante que o user_id existe
        ]);

        // Garante que o lançamento é para o usuário logado
        if ($request->user_id != Auth::id()) {
            return redirect()->back()->with('error', 'Você não tem permissão para adicionar lançamentos para outro usuário.');
        }

        Contracheque::create([
            'user_id' => $request->user_id,
            'data' => $request->data,
            'tipo_lancamento' => $request->tipo_lancamento,
            'valor' => $request->valor,
            'descricao' => $request->descricao,
        ]);

        // Redireciona de volta para o contracheque do mês/ano do lançamento
        return redirect()->route('financeiro.contracheque.index', [
            'mes' => Carbon::parse($request->data)->month,
            'ano' => Carbon::parse($request->data)->year
        ])->with('success', 'Lançamento de contracheque adicionado com sucesso!');
    }

    /**
     * Remove o lançamento de contracheque especificado do armazenamento.
     *
     * @param  \App\Models\Financeiro\Contracheque  $contracheque
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Contracheque $contracheque)
    {
        // Garante que o usuário logado só pode excluir seus próprios lançamentos
        if ($contracheque->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Você não tem permissão para excluir este lançamento.');
        }

        $contracheque->delete();

        return redirect()->back()->with('success', 'Lançamento de contracheque excluído com sucesso!');
    }
}
