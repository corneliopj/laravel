<?php

namespace App\Http\Controllers;

use App\Models\Incubacao;
use App\Models\Ave;
use App\Models\TipoAve;
use App\Models\Lote;
use App\Models\PosturaOvo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IncubacaoController extends Controller
{
    /**
     * Exibe uma lista de incubações.
     */
    public function index(Request $request)
    {
        $query = Incubacao::query();

        $query->with(['tipoAve', 'lote', 'posturaOvo']);

        if ($request->has('status') && $request->status != '') {
            $query->where('ativo', $request->status == 'ativo' ? true : false);
        }

        if ($request->filled('data_entrada_inicio')) {
            $query->where('data_entrada_incubadora', '>=', Carbon::parse($request->data_entrada_inicio)->startOfDay());
        }

        if ($request->filled('data_entrada_fim')) {
            $query->where('data_entrada_incubadora', '<=', Carbon::parse($request->data_entrada_fim)->endOfDay());
        }

        if ($request->filled('tipo_ave_id')) {
            $query->whereHas('tipoAve', function ($q) use ($request) {
                $q->where('id', $request->tipo_ave_id);
            });
        }

        $incubações = $query->orderBy('ativo', 'desc')
                            ->orderBy('data_entrada_incubadora', 'desc')
                            ->paginate(15);

        $tiposAve = TipoAve::orderBy('nome')->get();

        // Variável passada para a view com cedilha
        return view('incubacoes.listar', compact('incubações', 'tiposAve', 'request'));
    }

    /**
     * Mostra o formulário para criar uma nova incubação.
     */
    public function create()
    {
        $tiposAve = TipoAve::orderBy('nome')->get();
        $matrizes = Ave::where('sexo', 'Fêmea')->where('ativo', true)->get();
        $reprodutores = Ave::where('sexo', 'Macho')->where('ativo', true)->get();
        $lotes = Lote::orderBy('identificacao_lote')->get();
        $posturaOvos = PosturaOvo::all();

        return view('incubacao.create', compact('tiposAve', 'matrizes', 'reprodutores', 'lotes', 'posturaOvos'));
    }

    /**
     * Armazena uma nova incubação no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_ave_id' => 'required|exists:tipos_ave,id',
            'data_entrada_incubadora' => 'required|date|before_or_equal:today',
            'quantidade_ovos' => 'required|integer|min:1',
            'observacoes' => 'nullable|string|max:1000',
            'matriz_id' => 'nullable|exists:aves,id',
            'reprodutor_id' => 'nullable|exists:aves,id',
            'chocadeira' => 'nullable|string|max:255',
            'lote_id' => 'nullable|exists:lotes,id',
            'postura_ovo_id' => 'nullable|exists:postura_ovos,id',
        ]);

        try {
            Incubacao::create([
                'tipo_ave_id' => $request->tipo_ave_id,
                'data_entrada_incubadora' => $request->data_entrada_incubadora,
                'quantidade_ovos' => $request->quantidade_ovos,
                'observacoes' => $request->observacoes,
                'matriz_id' => $request->matriz_id,
                'reprodutor_id' => $request->reprodutor_id,
                'chocadeira' => $request->chocadeira,
                'lote_id' => $request->lote_id,
                'postura_ovo_id' => $request->postura_ovo_id,
                'data_prevista_eclosao' => Carbon::parse($request->data_entrada_incubadora)->addDays(21),
                'ativo' => true,
                'quantidade_eclodidos' => 0,
                'quantidade_inferteis' => 0,
                'quantidade_infectados' => 0,
                'quantidade_mortos' => 0,
            ]);

            return redirect()->route('incubacoes.index')->with('success', 'Incubação registrada com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro ao registrar incubação: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao registrar incubação: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de uma incubação específica.
     */
    public function show(string $id)
    {
        $incubacao = Incubacao::with(['tipoAve', 'matriz', 'reprodutor', 'lote', 'posturaOvo'])->findOrFail($id);
        return view('incubacao.show', compact('incubacao'));
    }

    /**
     * Mostra o formulário para editar uma incubação existente.
     */
    public function edit(string $id)
    {
        $incubacao = Incubacao::with(['lote', 'posturaOvo'])->findOrFail($id);
        $tiposAve = TipoAve::orderBy('nome')->get();
        $matrizes = Ave::where('sexo', 'Fêmea')->where('ativo', true)->get();
        $reprodutores = Ave::where('sexo', 'Macho')->where('ativo', true)->get();
        $lotes = Lote::orderBy('identificacao_lote')->get();
        $posturaOvos = PosturaOvo::all();

        return view('incubacao.edit', compact('incubacao', 'tiposAve', 'matrizes', 'reprodutores', 'lotes', 'posturaOvos'));
    }

    /**
     * Atualiza uma incubação existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $incubacao = Incubacao::findOrFail($id);

        $request->validate([
            'tipo_ave_id' => 'required|exists:tipos_ave,id',
            'data_entrada_incubadora' => 'required|date|before_or_equal:today',
            'quantidade_ovos' => 'required|integer|min:1',
            'observacoes' => 'nullable|string|max:1000',
            'matriz_id' => 'nullable|exists:aves,id',
            'reprodutor_id' => 'nullable|exists:aves,id',
            'chocadeira' => 'nullable|string|max:255',
            'lote_id' => 'nullable|exists:lotes,id',
            'postura_ovo_id' => 'nullable|exists:postura_ovos,id',
            'ativo' => 'required|boolean',
            'quantidade_eclodidos' => 'required|integer|min:0|max:' . $request->quantidade_ovos,
            'quantidade_inferteis' => 'required|integer|min:0|max:' . $request->quantidade_ovos,
            'quantidade_infectados' => 'required|integer|min:0|max:' . $request->quantidade_ovos,
            'quantidade_mortos' => 'required|integer|min:0|max:' . $request->quantidade_ovos,
        ]);

        $totalResultados = $request->quantidade_eclodidos + $request->quantidade_inferteis + $request->quantidade_infectados + $request->quantidade_mortos;
        if ($totalResultados > $request->quantidade_ovos) {
            return redirect()->back()->withInput()->with('error', 'A soma dos ovos eclodidos, inférteis, infectados e mortos não pode exceder a quantidade total de ovos.');
        }

        try {
            $incubacao->update([
                'tipo_ave_id' => $request->tipo_ave_id,
                'data_entrada_incubadora' => $request->data_entrada_incubadora,
                'quantidade_ovos' => $request->quantidade_ovos,
                'observacoes' => $request->observacoes,
                'matriz_id' => $request->matriz_id,
                'reprodutor_id' => $request->reprodutor_id,
                'chocadeira' => $request->chocadeira,
                'lote_id' => $request->lote_id,
                'postura_ovo_id' => $request->postura_ovo_id,
                'ativo' => $request->ativo,
                'quantidade_eclodidos' => $request->quantidade_eclodidos,
                'quantidade_inferteis' => $request->quantidade_inferteis,
                'quantidade_infectados' => $request->quantidade_infectados,
                'quantidade_mortos' => $request->quantidade_mortos,
                'data_prevista_eclosao' => Carbon::parse($request->data_entrada_incubadora)->addDays(21),
            ]);

            return redirect()->route('incubacoes.index')->with('success', 'Incubação atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar incubação: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar incubação: ' . $e->getMessage());
        }
    }

    /**
     * Inativa uma incubação do banco de dados (conforme o comportamento do botão "Inativar").
     */
    public function destroy(string $id)
    {
        try {
            $incubacao = Incubacao::findOrFail($id);
            $incubacao->ativo = false;
            $incubacao->save();

            return redirect()->route('incubacoes.index')->with('success', 'Incubação inativada com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro ao inativar incubação: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->with('error', 'Erro ao inativar incubação: ' . $e->getMessage());
        }
    }
}
