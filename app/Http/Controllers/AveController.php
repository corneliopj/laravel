<?php

namespace App\Http\Controllers;

use App\Models\Ave;
use App\Models\Morte;
use App\Models\TipoAve;
use App\Models\Variacao;
use App\Models\Lote;
use App\Models\Incubacao;
use App\Http\Requests\StoreAveRequest;
use App\Http\Requests\UpdateAveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\AveService;

class AveController extends Controller
{
    protected $aveService;

    public function __construct(AveService $aveService)
    {
        $this->aveService = $aveService;
    }

    /**
     * Exibe uma lista de aves.
     */
    public function index(Request $request)
    {
        // Se a requisição for AJAX (para sugestões de busca), retorna JSON
        if ($request->ajax()) {
            $query = $request->get('q');
            $aves = Ave::where('matricula', 'like', '%' . $query . '%')
                        ->orWhereHas('tipoAve', function ($q) use ($query) {
                            $q->where('nome', 'like', '%' . $query . '%');
                        })
                        ->get(['id', 'matricula']); // Seleciona apenas id e matricula

            return response()->json($aves);
        }

        $aves = $this->aveService->paginateAves($request);

        $tiposAves = TipoAve::all();
        $variacoes = Variacao::all();

        return view('aves.listar', compact('aves', 'tiposAves', 'variacoes'));
    }

    /**
     * Exibe o formulário para criar uma nova ave.
     */
    public function create()
    {
        $tiposAves = TipoAve::all();
        $variacoes = Variacao::all();
        $lotes = Lote::all();
        $incubacoes = Incubacao::all();

        return view('aves.criar', compact('tiposAves', 'variacoes', 'lotes', 'incubacoes'));
    }

    /**
     * Armazena uma nova ave no banco de dados.
     */
    public function store(StoreAveRequest $request)
    {
        $data = $request->except('foto');
        $this->aveService->storeAve($data, $request->file('foto'));

        return redirect()->route('aves.index')->with('success', 'Ave cadastrada com sucesso!');
    }

    /**
     * Exibe os detalhes de uma ave específica.
     */
    public function show(Ave $ave)
    {
        $ave->load([
            'tipoAve',
            'variacao',
            'lote',
            'mortes',
            'incubacao.posturaOvo.acasalamento.macho',
            'incubacao.posturaOvo.acasalamento.femea'
        ]);
        return view('aves.ficha', compact('ave'));
    }

    /**
     * Exibe o formulário para editar uma ave.
     */
    public function edit(Ave $ave)
    {
        $tiposAves = TipoAve::all();
        $variacoes = Variacao::all();
        $lotes = Lote::all();
        $incubacoes = Incubacao::all();
        return view('aves.editar', compact('ave', 'tiposAves', 'variacoes', 'lotes', 'incubacoes'));
    }

    /**
     * Atualiza uma ave no banco de dados.
     */
    public function update(UpdateAveRequest $request, Ave $ave)
    {
        $data = $request->except('foto');
        $this->aveService->updateAve(
            $ave, 
            $data, 
            $request->file('foto'), 
            $request->input('remover_foto_atual') == 1
        );

        return redirect()->route('aves.index')->with('success', 'Ave atualizada com sucesso!');
    }

    /**
     * Soft-deleta uma ave (marca como inativa).
     */
    public function destroy(Ave $ave)
    {
        $this->aveService->deleteAve($ave);

        return redirect()->route('aves.index')->with('success', 'Ave inativada e marcada para exclusão (soft delete) com sucesso!');
    }

    /**
     * Restaura uma ave soft-deletada.
     */
    public function restore($id)
    {
        $this->aveService->restoreAve($id);

        return redirect()->route('aves.index')->with('success', 'Ave restaurada com sucesso!');
    }

    /**
     * Exclui permanentemente uma ave.
     */
    public function forceDelete($id)
    {
        $this->aveService->forceDeleteAve($id);

        return redirect()->route('aves.index')->with('success', 'Ave excluída permanentemente com sucesso!');
    }

    /**
     * Exibe o formulário para registrar a morte de uma ave.
     */
    public function registerDeath(Ave $ave)
    {
        if ($ave->mortes()->exists()) {
            return redirect()->route('aves.show', $ave->id)->with('error', 'Esta ave já possui um registro de morte.');
        }

        return view('aves.registrar_morte', compact('ave'));
    }

    /**
     * Armazena o registro de morte de uma ave.
     */
    public function storeDeath(Request $request, Ave $ave)
    {
        $request->validate([
            'data_morte' => 'required|date|before_or_equal:today',
            'causa' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->aveService->registerDeath($ave, $request->all());
        } catch (\Exception $e) {
            return redirect()->route('aves.show', $ave->id)->with('error', $e->getMessage());
        }

        return redirect()->route('aves.show', $ave->id)->with('success', 'Morte da ave registrada com sucesso.');
    }

    /**
     * Gera e associa um código de validação à certidão da ave.
     */
    public function expedirCertidao(Ave $ave)
    {
        $validationCode = $this->aveService->expedirCertidao($ave);

        if (!$validationCode) {
            return redirect()->back()->with('error', 'Não foi possível gerar um código de validação único para a certidão após várias tentativas. Por favor, tente novamente.');
        }

        return redirect()->route('certidao.show', ['validation_code' => $validationCode])->with('success', 'Certidão emitida com sucesso! O código de validação é: ' . $validationCode);
    }

    /**
     * Exibe a certidão pública usando o código de validação.
     */
    public function showCertidao(string $validation_code)
    {
        $validation_code = strtoupper($validation_code);

        $ave = Ave::withTrashed()->with('mortes')->where('codigo_validacao_certidao', $validation_code)->first();

        if (!$ave) {
            return redirect('/')->with('error', 'Certidão não encontrada ou código de validação inválido.');
        }

        $ave->load(['tipoAve', 'variacao', 'lote', 'incubacao.posturaOvo.acasalamento.macho', 'incubacao.posturaOvo.acasalamento.femea']);

        return view('certidao.show', compact('ave'));
    }

    /**
     * Exibe o formulário de validação de certidão para usuários externos.
     */
    public function showValidarForm()
    {
        return view('certidao.validar');
    }

    /**
     * Processa a submissão do formulário de validação de certidão.
     */
    public function processValidarForm(Request $request)
    {
        $request->validate([
            'matricula' => 'required|string|max:255',
            'codigo_validacao' => 'required|string|size:11',
        ]);

        $ave = $this->aveService->validateCertidao(
            $request->input('matricula'), 
            $request->input('codigo_validacao')
        );

        if ($ave) {
            return redirect()->route('certidao.show', ['validation_code' => $ave->codigo_validacao_certidao])
                                ->with('success', 'Certidão validada com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Matrícula ou Código de Validação inválidos ou não correspondentes.');
        }
    }

    /**
     * Fornece sugestões de pesquisa para aves com base na matrícula e tipo de ave.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->input('query');
        $suggestions = $this->aveService->searchSuggestions($query);
        
        return response()->json($suggestions);
    }

    /**
     * Realiza a busca completa de aves com base na matrícula ou outros termos.
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        if (empty($query)) {
            return redirect()->route('aves.index')->with('error', 'Por favor, insira um termo para buscar.');
        }

        $aves = $this->aveService->searchAves($query);

        if (!$aves || $aves->isEmpty()) {
            return redirect()->route('aves.index')->with('error', 'Nenhuma ave encontrada para o termo "' . $query . '".');
        }

        if ($aves->count() === 1 && $aves->first()->matricula === $query) {
            return redirect()->route('aves.show', $aves->first()->id);
        }

        return view('aves.listar', compact('aves', 'query'));
    }
}
