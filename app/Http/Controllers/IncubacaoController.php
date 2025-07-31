<?php

namespace App\Http\Controllers;

use App\Models\Incubacao;
use App\Models\Ave;
use App\Models\TipoAve;
use App\Models\Lote;
use App\Models\PosturaOvo; // Certifique-se de que o modelo PosturaOvo está importado
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

        $incubacoes = $query->orderBy('ativo', 'desc')
                            ->orderBy('data_entrada_incubadora', 'desc')
                            ->paginate(10);

        $tiposAves = TipoAve::orderBy('nome')->get(); // Para o filtro
        $statusOptions = ['ativo' => 'Ativo', 'inativo' => 'Inativo']; // Para o filtro

        return view('incubacoes.index', compact('incubacoes', 'tiposAves', 'statusOptions', 'request'));
    }

    /**
     * Mostra o formulário para criar uma nova incubação.
     */
    public function create()
    {
        $tiposAves = TipoAve::orderBy('nome')->get();
        $lotes = Lote::where('ativo', true)->orderBy('identificacao_lote')->get();
        // CORREÇÃO AQUI: Usar o escopo 'ativas' do modelo PosturaOvo
        $posturasOvos = PosturaOvo::ativas()->orderBy('data_postura', 'desc')->get();

        return view('incubacoes.create', compact('tiposAves', 'lotes', 'posturasOvos'));
    }

    /**
     * Armazena uma nova incubação no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lote_ovos_id' => 'nullable|exists:lotes,id',
            'tipo_ave_id' => 'required|exists:tipos_aves,id',
            'postura_ovo_id' => 'nullable|exists:posturas_ovos,id',
            'data_entrada_incubadora' => 'required|date',
            'quantidade_ovos' => 'required|integer|min:1',
            'chocadeira' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'ativo' => 'boolean',
        ]);

        try {
            $tipoAve = TipoAve::findOrFail($request->tipo_ave_id);
            $tempoEclosao = $tipoAve->tempo_eclosao ?? 21; // Pega o tempo_eclosao do tipo de ave, padrão 21 dias

            $dataPrevistaEclosao = Carbon::parse($request->data_entrada_incubadora)->addDays($tempoEclosao);

            Incubacao::create([
                'lote_ovos_id' => $request->lote_ovos_id,
                'tipo_ave_id' => $request->tipo_ave_id,
                'postura_ovo_id' => $request->postura_ovo_id,
                'data_entrada_incubadora' => $request->data_entrada_incubadora,
                'data_prevista_eclosao' => $dataPrevistaEclosao,
                'quantidade_ovos' => $request->quantidade_ovos,
                'chocadeira' => $request->chocadeira,
                'observacoes' => $request->observacoes,
                'ativo' => $request->boolean('ativo'),
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
    public function show(Incubacao $incubacao)
    {
        $incubacao->load(['tipoAve', 'lote', 'posturaOvo']);
        return view('incubacoes.show', compact('incubacao'));
    }

    /**
     * Mostra o formulário para editar uma incubação existente.
     */
    public function edit(Incubacao $incubacao)
    {
        $tiposAves = TipoAve::orderBy('nome')->get();
        $lotes = Lote::where('ativo', true)->orderBy('identificacao_lote')->get();
        // CORREÇÃO AQUI: Usar o escopo 'ativas' do modelo PosturaOvo
        $posturasOvos = PosturaOvo::ativas()->orderBy('data_postura', 'desc')->get();

        return view('incubacoes.edit', compact('incubacao', 'tiposAves', 'lotes', 'posturasOvos'));
    }

    /**
     * Atualiza uma incubação existente no banco de dados.
     */
    public function update(Request $request, Incubacao $incubacao)
    {
        $request->validate([
            'lote_ovos_id' => 'nullable|exists:lotes,id',
            'tipo_ave_id' => 'required|exists:tipos_aves,id',
            'postura_ovo_id' => 'nullable|exists:posturas_ovos,id',
            'data_entrada_incubadora' => 'required|date',
            'quantidade_ovos' => 'required|integer|min:1',
            'quantidade_eclodidos' => 'nullable|integer|min:0|max:' . $request->quantidade_ovos,
            'quantidade_inferteis' => 'nullable|integer|min:0',
            'quantidade_infectados' => 'nullable|integer|min:0',
            'quantidade_mortos' => 'nullable|integer|min:0',
            'chocadeira' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'ativo' => 'boolean',
        ]);

        try {
            $tipoAve = TipoAve::findOrFail($request->tipo_ave_id);
            $tempoEclosao = $tipoAve->tempo_eclosao ?? 21;

            $dataPrevistaEclosao = Carbon::parse($request->data_entrada_incubadora)->addDays($tempoEclosao);

            $incubacao->update([
                'lote_ovos_id' => $request->lote_ovos_id,
                'tipo_ave_id' => $request->tipo_ave_id,
                'postura_ovo_id' => $request->postura_ovo_id,
                'data_entrada_incubadora' => $request->data_entrada_incubadora,
                'data_prevista_eclosao' => $dataPrevistaEclosao,
                'quantidade_ovos' => $request->quantidade_ovos,
                'quantidade_eclodidos' => $request->quantidade_eclodidos,
                'quantidade_inferteis' => $request->quantidade_inferteis,
                'quantidade_infectados' => $request->quantidade_infectados,
                'quantidade_mortos' => $request->quantidade_mortos,
                'chocadeira' => $request->chocadeira,
                'observacoes' => $request->observacoes,
                'ativo' => $request->boolean('ativo'),
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

    /**
     * Exibe a ficha de incubação para impressão ou visualização.
     */
    public function ficha(Incubacao $incubacao)
    {
        $incubacao->load(['tipoAve', 'lote', 'posturaOvo']);
        return view('incubacoes.ficha', compact('incubacao'));
    }
}
