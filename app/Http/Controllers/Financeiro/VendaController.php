<?php

namespace App\Http\Controllers\Financeiro; // Mantenha o namespace financeiro se for o caso

use App\Http\Controllers\Controller;
use App\Models\Financeiro\Venda; // Modelo de Venda
use App\Models\Ave; // Modelo de Ave individual
use App\Models\Plantel; // Modelo de Plantel
use App\Models\MovimentacaoPlantel; // Modelo de MovimentacaoPlantel
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendaController extends Controller
{
    /**
     * Exibe uma listagem de vendas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Carrega as vendas, incluindo os relacionamentos com Ave e Plantel (se existirem)
        // Ordena pela data da venda mais recente
        $vendas = Venda::with(['ave', 'plantel'])->orderBy('data_venda', 'desc')->paginate(15);
        return view('vendas.index', compact('vendas'));
    }

    /**
     * Mostra o formulário para registrar uma nova venda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        // Carrega aves ativas para seleção individual
        $aves = Ave::where('ativo', true)->orderBy('matricula')->get();
        // Carrega plantéis ativos para seleção em grupo
        $plantelOptions = Plantel::where('ativo', true)->orderBy('identificacao_grupo')->get();

        // Se veio de um link de ave específica, pré-seleciona a ave
        $preSelectedAveId = $request->query('ave_id');
        $preSelectedPlantelId = $request->query('plantel_id');

        return view('vendas.create', compact('aves', 'plantelOptions', 'preSelectedAveId', 'preSelectedPlantelId'));
    }

    /**
     * Armazena um novo registro de venda no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_registro' => 'required|in:individual,plantel', // Campo para diferenciar o tipo de venda
            'data_venda' => 'required|date|before_or_equal:today',
            'valor_venda' => 'required|numeric|min:0.01',
            'comprador' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            // Validações condicionais
            'ave_id' => 'required_if:tipo_registro,individual|exists:aves,id',
            'plantel_id' => 'required_if:tipo_registro,plantel|exists:plantel,id',
            'quantidade_venda_plantel' => 'required_if:tipo_registro,plantel|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $venda = Venda::create([
                'data_venda' => $request->data_venda,
                'valor_venda' => $request->valor_venda,
                'comprador' => $request->comprador,
                'observacoes' => $request->observacoes,
            ]);

            if ($request->tipo_registro == 'individual') {
                $ave = Ave::findOrFail($request->ave_id);
                $ave->ativo = false; // Inativa a ave individual (ou marca como vendida)
                $ave->save();
                $venda->ave_id = $ave->id; // Associa a venda à ave
                $venda->save(); // Salva a associação
                $message = 'Venda de ave individual registrada com sucesso!';

            } elseif ($request->tipo_registro == 'plantel') {
                $plantel = Plantel::findOrFail($request->plantel_id);

                // Verifica se a quantidade de venda não excede a quantidade atual do plantel
                if ($request->quantidade_venda_plantel > $plantel->quantidade_atual) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'A quantidade de venda não pode exceder a quantidade atual do plantel (' . $plantel->quantidade_atual . ').');
                }

                // Cria uma movimentação de saída para o plantel
                MovimentacaoPlantel::create([
                    'plantel_id' => $plantel->id,
                    'tipo_movimentacao' => 'saida_venda',
                    'quantidade' => $request->quantidade_venda_plantel,
                    'data_movimentacao' => $request->data_venda,
                    'observacoes' => 'Venda de ' . $request->quantidade_venda_plantel . ' aves do plantel. Comprador: ' . ($request->comprador ?? 'Não informado'),
                ]);
                $venda->plantel_id = $plantel->id; // Associa a venda ao plantel
                $venda->quantidade_venda_plantel = $request->quantidade_venda_plantel; // Armazena a quantidade de venda do plantel
                $venda->save(); // Salva a associação

                $message = 'Venda de aves em plantel registrada com sucesso!';
            }

            DB::commit(); // Confirma a transação
            return redirect()->route('vendas.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack(); // Reverte a transação em caso de erro
            Log::error("Erro ao registrar venda: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao registrar venda: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de um registro de venda.
     *
     * @param  \App\Models\Financeiro\Venda  $venda
     * @return \Illuminate\View\View
     */
    public function show(Venda $venda)
    {
        // Carrega os relacionamentos para exibir os detalhes
        $venda->load('ave', 'plantel');
        return view('vendas.show', compact('venda'));
    }

    /**
     * Mostra o formulário para editar um registro de venda existente.
     *
     * @param  \App\Models\Financeiro\Venda  $venda
     * @return \Illuminate\View\View
     */
    public function edit(Venda $venda)
    {
        // Carrega aves ativas e plantéis para os dropdowns
        $aves = Ave::where('ativo', true)->orderBy('matricula')->get();
        $plantelOptions = Plantel::where('ativo', true)->orderBy('identificacao_grupo')->get();

        // Determina o tipo de registro atual para pré-selecionar no formulário
        $tipoRegistroAtual = $venda->ave_id ? 'individual' : ($venda->plantel_id ? 'plantel' : '');

        return view('vendas.edit', compact('venda', 'aves', 'plantelOptions', 'tipoRegistroAtual'));
    }

    /**
     * Atualiza um registro de venda existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Financeiro\Venda  $venda
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Venda $venda)
    {
        $request->validate([
            'tipo_registro' => 'required|in:individual,plantel',
            'data_venda' => 'required|date|before_or_equal:today',
            'valor_venda' => 'required|numeric|min:0.01',
            'comprador' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'ave_id' => 'required_if:tipo_registro,individual|exists:aves,id',
            'plantel_id' => 'required_if:tipo_registro,plantel|exists:plantel,id',
            'quantidade_venda_plantel' => 'required_if:tipo_registro,plantel|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Reverte o estado anterior antes de aplicar as novas mudanças
            if ($venda->ave_id) {
                $oldAve = Ave::find($venda->ave_id);
                if ($oldAve) {
                    $oldAve->ativo = true; // Reativa a ave anterior
                    $oldAve->save();
                }
            } elseif ($venda->plantel_id && $venda->quantidade_venda_plantel) {
                // Para edição de vendas de plantel, a reversão é mais complexa.
                // Idealmente, uma movimentação de "ajuste" seria criada
                // ou a movimentação original seria editada.
                MovimentacaoPlantel::create([
                    'plantel_id' => $venda->plantel_id,
                    'tipo_movimentacao' => 'entrada', // Ajuste para reverter a venda anterior
                    'quantidade' => $venda->quantidade_venda_plantel,
                    'data_movimentacao' => Carbon::now(), // Data do ajuste
                    'observacoes' => 'Ajuste de reversão de venda anterior (ID Venda: ' . $venda->id . ') devido a edição.',
                ]);
            }

            // Atualiza o registro de venda
            $venda->update([
                'data_venda' => $request->data_venda,
                'valor_venda' => $request->valor_venda,
                'comprador' => $request->comprador,
                'observacoes' => $request->observacoes,
                'ave_id' => null, // Reseta para garantir que apenas um tipo seja associado
                'plantel_id' => null,
                'quantidade_venda_plantel' => null,
            ]);

            if ($request->tipo_registro == 'individual') {
                $ave = Ave::findOrFail($request->ave_id);
                $ave->ativo = false; // Inativa a nova ave
                $ave->save();
                $venda->ave_id = $ave->id;
                $venda->save();
                $message = 'Venda de ave individual atualizada com sucesso!';

            } elseif ($request->tipo_registro == 'plantel') {
                $plantel = Plantel::findOrFail($request->plantel_id);

                // Verifica se a quantidade de venda não excede a quantidade atual do plantel
                if ($request->quantidade_venda_plantel > $plantel->quantidade_atual + ($venda->quantidade_venda_plantel ?? 0)) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'A quantidade de venda não pode exceder a quantidade atual do plantel.');
                }

                MovimentacaoPlantel::create([
                    'plantel_id' => $plantel->id,
                    'tipo_movimentacao' => 'saida_venda',
                    'quantidade' => $request->quantidade_venda_plantel,
                    'data_movimentacao' => $request->data_venda,
                    'observacoes' => 'Venda de ' . $request->quantidade_venda_plantel . ' aves do plantel (Atualização de registro de venda ID: ' . $venda->id . '). Comprador: ' . ($request->comprador ?? 'Não informado'),
                ]);
                $venda->plantel_id = $plantel->id;
                $venda->quantidade_venda_plantel = $request->quantidade_venda_plantel;
                $venda->save();
                $message = 'Venda de aves em plantel atualizada com sucesso!';
            }

            DB::commit();
            return redirect()->route('vendas.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar registro de venda: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar registro de venda: ' . $e->getMessage());
        }
    }

    /**
     * Remove um registro de venda do banco de dados.
     *
     * @param  \App\Models\Financeiro\Venda  $venda
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Venda $venda)
    {
        DB::beginTransaction();
        try {
            if ($venda->ave_id) {
                $ave = Ave::find($venda->ave_id);
                if ($ave) {
                    $ave->ativo = true; // Reativa a ave ao deletar o registro de venda individual
                    $ave->save();
                }
            } elseif ($venda->plantel_id && $venda->quantidade_venda_plantel) {
                // Ao deletar uma venda de plantel, cria uma movimentação de "entrada" para reverter
                MovimentacaoPlantel::create([
                    'plantel_id' => $venda->plantel_id,
                    'tipo_movimentacao' => 'entrada',
                    'quantidade' => $venda->quantidade_venda_plantel,
                    'data_movimentacao' => Carbon::now(),
                    'observacoes' => 'Reversão de venda (ID Venda: ' . $venda->id . ') devido à exclusão do registro.',
                ]);
            }

            $venda->delete(); // Exclui o registro de venda
            DB::commit();
            return redirect()->route('vendas.index')->with('success', 'Registro de venda excluído com sucesso e status revertido!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao excluir registro de venda: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->with('error', 'Erro ao excluir registro de venda: ' . $e->getMessage());
        }
    }
}
