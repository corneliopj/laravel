<?php

namespace App\Http\Controllers;

use App\Models\Morte; // Modelo para registros de morte
use App\Models\Ave;   // Modelo para aves individuais
use App\Models\Plantel; // Modelo para plantéis agrupados
use App\Models\MovimentacaoPlantel; // Modelo para movimentações de plantel
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Para transações
use Illuminate\Support\Facades\Log; // Para log de erros

class MorteController extends Controller
{
    /**
     * Exibe uma listagem de registros de morte.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Carrega as mortes, incluindo os relacionamentos com Ave e Plantel (se existirem)
        // Ordena pela data da morte mais recente
        $mortes = Morte::with(['ave', 'plantel'])->orderBy('data_morte', 'desc')->paginate(15);
        return view('mortes.index', compact('mortes'));
    }

    /**
     * Mostra o formulário para registrar uma nova morte.
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

        return view('mortes.create', compact('aves', 'plantelOptions', 'preSelectedAveId', 'preSelectedPlantelId'));
    }

    /**
     * Armazena um novo registro de morte no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_registro' => 'required|in:individual,plantel', // Campo para diferenciar o tipo de morte
            'data_morte' => 'required|date|before_or_equal:today',
            'causa_morte' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            // Validações condicionais
            'ave_id' => 'required_if:tipo_registro,individual|exists:aves,id',
            'plantel_id' => 'required_if:tipo_registro,plantel|exists:plantel,id',
            'quantidade_mortes_plantel' => 'required_if:tipo_registro,plantel|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $morte = Morte::create([
                'data_morte' => $request->data_morte,
                'causa_morte' => $request->causa_morte,
                'observacoes' => $request->observacoes,
            ]);

            if ($request->tipo_registro == 'individual') {
                $ave = Ave::findOrFail($request->ave_id);
                $ave->ativo = false; // Inativa a ave individual
                $ave->save();
                $morte->ave_id = $ave->id; // Associa a morte à ave
                $morte->save(); // Salva a associação
                $message = 'Morte de ave individual registrada com sucesso!';

            } elseif ($request->tipo_registro == 'plantel') {
                $plantel = Plantel::findOrFail($request->plantel_id);

                // Verifica se a quantidade de mortes não excede a quantidade atual do plantel
                if ($request->quantidade_mortes_plantel > $plantel->quantidade_atual) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'A quantidade de mortes não pode exceder a quantidade atual do plantel (' . $plantel->quantidade_atual . ').');
                }

                // Cria uma movimentação de saída para o plantel
                MovimentacaoPlantel::create([
                    'plantel_id' => $plantel->id,
                    'tipo_movimentacao' => 'saida_morte',
                    'quantidade' => $request->quantidade_mortes_plantel,
                    'data_movimentacao' => $request->data_morte,
                    'observacoes' => 'Morte de ' . $request->quantidade_mortes_plantel . ' aves do plantel. Causa: ' . ($request->causa_morte ?? 'Não informada'),
                ]);
                $morte->plantel_id = $plantel->id; // Associa a morte ao plantel
                $morte->quantidade_mortes_plantel = $request->quantidade_mortes_plantel; // Armazena a quantidade de mortes do plantel
                $morte->save(); // Salva a associação

                $message = 'Morte de aves em plantel registrada com sucesso!';
            }

            DB::commit(); // Confirma a transação
            return redirect()->route('mortes.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack(); // Reverte a transação em caso de erro
            Log::error("Erro ao registrar morte: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao registrar morte: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de um registro de morte.
     *
     * @param  \App\Models\Morte  $morte
     * @return \Illuminate\View\View
     */
    public function show(Morte $morte)
    {
        // Carrega os relacionamentos para exibir os detalhes
        $morte->load('ave', 'plantel');
        return view('mortes.show', compact('morte'));
    }

    /**
     * Mostra o formulário para editar um registro de morte existente.
     *
     * @param  \App\Models\Morte  $morte
     * @return \Illuminate\View\View
     */
    public function edit(Morte $morte)
    {
        // Carrega aves ativas e plantéis para os dropdowns
        $aves = Ave::where('ativo', true)->orderBy('matricula')->get();
        $plantelOptions = Plantel::where('ativo', true)->orderBy('identificacao_grupo')->get();

        // Determina o tipo de registro atual para pré-selecionar no formulário
        $tipoRegistroAtual = $morte->ave_id ? 'individual' : ($morte->plantel_id ? 'plantel' : '');

        return view('mortes.edit', compact('morte', 'aves', 'plantelOptions', 'tipoRegistroAtual'));
    }

    /**
     * Atualiza um registro de morte existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Morte  $morte
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Morte $morte)
    {
        $request->validate([
            'tipo_registro' => 'required|in:individual,plantel',
            'data_morte' => 'required|date|before_or_equal:today',
            'causa_morte' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'ave_id' => 'required_if:tipo_registro,individual|exists:aves,id',
            'plantel_id' => 'required_if:tipo_registro,plantel|exists:plantel,id',
            'quantidade_mortes_plantel' => 'required_if:tipo_registro,plantel|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Reverte o estado anterior antes de aplicar as novas mudanças
            // Isso é crucial para a integridade dos dados de plantel
            if ($morte->ave_id) {
                $oldAve = Ave::find($morte->ave_id);
                if ($oldAve) {
                    $oldAve->ativo = true; // Reativa a ave anterior
                    $oldAve->save();
                }
            } elseif ($morte->plantel_id && $morte->quantidade_mortes_plantel) {
                // Para edição de mortes de plantel, a reversão é mais complexa.
                // Idealmente, uma movimentação de "ajuste" seria criada
                // ou a movimentação original seria editada.
                // Para simplificar agora, vamos apenas criar uma nova movimentação de ajuste.
                // Uma solução mais robusta seria editar a MovimentacaoPlantel original.
                MovimentacaoPlantel::create([
                    'plantel_id' => $morte->plantel_id,
                    'tipo_movimentacao' => 'entrada', // Ajuste para reverter a morte anterior
                    'quantidade' => $morte->quantidade_mortes_plantel,
                    'data_movimentacao' => Carbon::now(), // Data do ajuste
                    'observacoes' => 'Ajuste de reversão de morte anterior (ID Morte: ' . $morte->id . ') devido a edição.',
                ]);
            }

            // Atualiza o registro de morte
            $morte->update([
                'data_morte' => $request->data_morte,
                'causa_morte' => $request->causa_morte,
                'observacoes' => $request->observacoes,
                'ave_id' => null, // Reseta para garantir que apenas um tipo seja associado
                'plantel_id' => null,
                'quantidade_mortes_plantel' => null,
            ]);

            if ($request->tipo_registro == 'individual') {
                $ave = Ave::findOrFail($request->ave_id);
                $ave->ativo = false; // Inativa a nova ave
                $ave->save();
                $morte->ave_id = $ave->id;
                $morte->save();
                $message = 'Morte de ave individual atualizada com sucesso!';

            } elseif ($request->tipo_registro == 'plantel') {
                $plantel = Plantel::findOrFail($request->plantel_id);

                // Verifica se a quantidade de mortes não excede a quantidade atual do plantel
                // (considerando a reversão feita acima, se aplicável)
                // Para uma validação mais precisa aqui, seria necessário recalcular a quantidade atual
                // após a reversão e antes da nova aplicação. Por simplicidade, assumimos que a reversão
                // é suficiente e a nova quantidade é válida.
                if ($request->quantidade_mortes_plantel > $plantel->quantidade_atual + ($morte->quantidade_mortes_plantel ?? 0)) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'A quantidade de mortes não pode exceder a quantidade atual do plantel.');
                }

                MovimentacaoPlantel::create([
                    'plantel_id' => $plantel->id,
                    'tipo_movimentacao' => 'saida_morte',
                    'quantidade' => $request->quantidade_mortes_plantel,
                    'data_movimentacao' => $request->data_morte,
                    'observacoes' => 'Morte de ' . $request->quantidade_mortes_plantel . ' aves do plantel (Atualização de registro de morte ID: ' . $morte->id . '). Causa: ' . ($request->causa_morte ?? 'Não informada'),
                ]);
                $morte->plantel_id = $plantel->id;
                $morte->quantidade_mortes_plantel = $request->quantidade_mortes_plantel;
                $morte->save();
                $message = 'Morte de aves em plantel atualizada com sucesso!';
            }

            DB::commit();
            return redirect()->route('mortes.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar registro de morte: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar registro de morte: ' . $e->getMessage());
        }
    }

    /**
     * Remove um registro de morte do banco de dados.
     *
     * @param  \App\Models\Morte  $morte
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Morte $morte)
    {
        DB::beginTransaction();
        try {
            if ($morte->ave_id) {
                $ave = Ave::find($morte->ave_id);
                if ($ave) {
                    $ave->ativo = true; // Reativa a ave ao deletar o registro de morte individual
                    $ave->save();
                }
            } elseif ($morte->plantel_id && $morte->quantidade_mortes_plantel) {
                // Ao deletar uma morte de plantel, cria uma movimentação de "entrada" para reverter
                MovimentacaoPlantel::create([
                    'plantel_id' => $morte->plantel_id,
                    'tipo_movimentacao' => 'entrada',
                    'quantidade' => $morte->quantidade_mortes_plantel,
                    'data_movimentacao' => Carbon::now(),
                    'observacoes' => 'Reversão de morte (ID Morte: ' . $morte->id . ') devido à exclusão do registro.',
                ]);
            }

            $morte->delete(); // Exclui o registro de morte
            DB::commit();
            return redirect()->route('mortes.index')->with('success', 'Registro de morte excluído com sucesso e status revertido!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao excluir registro de morte: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->with('error', 'Erro ao excluir registro de morte: ' . $e->getMessage());
        }
    }
}
