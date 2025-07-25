<?php

namespace App\Http\Controllers;

use App\Models\Ave;
use App\Models\Morte;
use App\Models\TipoAve;
use App\Models\Variacao;
use App\Models\Lote;
use App\Models\Incubacao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AveController extends Controller
{
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

        // Lógica de filtragem e paginação para a lista completa
        $query = Ave::query();
        $query->with(['tipoAve', 'variacao', 'lote', 'mortes']); // Carrega relações, incluindo 'mortes' (plural)

        $status = $request->input('status');

        switch ($status) {
            case 'ativas':
                $query->where('ativo', true)
                      ->doesntHave('mortes')
                      ->whereNull('deleted_at');
                break;
            case 'excluidas':
                $query->onlyTrashed();
                break;
            case 'mortas':
                $query->whereHas('mortes')
                      ->withTrashed();
                break;
            case 'inativas':
                $query->where('ativo', false)
                      ->doesntHave('mortes')
                      ->whereNull('deleted_at');
                break;
            default:
                $query->withTrashed();
                break;
        }

        // Filtro por tipo de ave
        if ($request->filled('tipo_ave_id')) {
            $query->where('tipo_ave_id', $request->tipo_ave_id);
        }

        // Filtro por variação
        if ($request->filled('variacao_id')) {
            $query->where('variacao_id', $request->variacao_id);
        }

        // Filtro por sexo
        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        // Busca por matrícula (se search for usado para busca geral ou searchbox)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('matricula', 'like', "%{$searchTerm}%");
        }

        // Ordenação
        $sortColumn = $request->get('sort', 'matricula');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortColumn, $sortDirection);


        $aves = $query->paginate(10); // Paginação de 10 aves por página

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
    public function store(Request $request)
    {
        $request->validate([
            'matricula' => 'required|string|max:255|unique:aves,matricula',
            'tipo_ave_id' => 'required|exists:tipos_aves,id',
            'variacao_id' => 'required|exists:variacoes,id',
            'sexo' => ['required', Rule::in(['Macho', 'Femea', 'Indefinido'])],
            'data_eclosao' => 'required|date|before_or_equal:today',
            'lote_id' => 'nullable|exists:lotes,id',
            'incubacao_id' => 'nullable|exists:incubacoes,id',
            'peso_nascimento' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->except('foto');
        $data['ativo'] = true;

        if ($request->hasFile('foto')) {
            $imagePath = $request->file('foto')->store('uploads/aves', 'public');
            $data['foto_path'] = $imagePath;
        } else {
            $data['foto_path'] = null;
        }

        Ave::create($data);

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
    public function update(Request $request, Ave $ave)
    {
        $request->validate([
            'matricula' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aves', 'matricula')->ignore($ave->id),
            ],
            'tipo_ave_id' => 'required|exists:tipos_aves,id',
            'variacao_id' => 'required|exists:variacoes,id',
            'sexo' => ['required', Rule::in(['Macho', 'Femea', 'Indefinido'])],
            'data_eclosao' => 'required|date|before_or_equal:today',
            'lote_id' => 'nullable|exists:lotes,id',
            'incubacao_id' => 'nullable|exists:incubacoes,id',
            'peso_nascimento' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'remover_foto_atual' => 'nullable|boolean',
        ]);

        $data = $request->except('foto');

        if ($request->has('remover_foto_atual') && $request->remover_foto_atual == 1) {
            if ($ave->foto_path && Storage::disk('public')->exists($ave->foto_path)) {
                Storage::disk('public')->delete($ave->foto_path);
            }
            $data['foto_path'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($ave->foto_path && Storage::disk('public')->exists($ave->foto_path)) {
                Storage::disk('public')->delete($ave->foto_path);
            }
            $imagePath = $request->file('foto')->store('uploads/aves', 'public');
            $data['foto_path'] = $imagePath;
        }

        $ave->update($data);

        return redirect()->route('aves.index')->with('success', 'Ave atualizada com sucesso!');
    }

    /**
     * Soft-deleta uma ave (marca como inativa).
     */
    public function destroy(Ave $ave)
    {
        $ave->ativo = false;
        $ave->save();
        $ave->delete();

        return redirect()->route('aves.index')->with('success', 'Ave inativada e marcada para exclusão (soft delete) com sucesso!');
    }

    /**
     * Restaura uma ave soft-deletada.
     */
    public function restore($id)
    {
        $ave = Ave::withTrashed()->findOrFail($id);
        $ave->restore();
        $ave->ativo = true;
        $ave->save();

        return redirect()->route('aves.index')->with('success', 'Ave restaurada com sucesso!');
    }

    /**
     * Exclui permanentemente uma ave.
     */
    public function forceDelete($id)
    {
        $ave = Ave::withTrashed()->findOrFail($id);

        if ($ave->foto_path && Storage::disk('public')->exists($ave->foto_path)) {
            Storage::disk('public')->delete($ave->foto_path);
        }

        $ave->forceDelete();

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

        if ($ave->mortes()->exists()) {
            return redirect()->route('aves.show', $ave->id)->with('error', 'Esta ave já possui um registro de morte.');
        }

        Morte::create([
            'ave_id' => $ave->id,
            'data_morte' => $request->input('data_morte'),
            'causa' => $request->input('causa'),
            'observacoes' => $request->input('observacoes'),
        ]);

        $ave->ativo = false;
        $ave->save();

        return redirect()->route('aves.show', $ave->id)->with('success', 'Morte da ave registrada com sucesso.');
    }

    /**
     * Gera e associa um código de validação à certidão da ave.
     * Implementa lógica de retentativa para garantir unicidade.
     */
    public function expedirCertidao(Ave $ave)
    {
        $maxRetries = 10;
        $attempt = 0;
        $saved = false;
        $validationCode = null;

        while (!$saved && $attempt < $maxRetries) {
            try {
                do {
                    $hexPart = bin2hex(random_bytes(5));
                    $code = strtoupper(substr($hexPart, 0, 5) . '-' . substr($hexPart, 5, 5));
                } while (Ave::where('codigo_validacao_certidao', $code)->exists() && $attempt < $maxRetries);

                if ($attempt >= $maxRetries) {
                    break;
                }

                $validationCode = $code;
                $ave->codigo_validacao_certidao = $validationCode;
                $ave->save();

                $saved = true;
            } catch (QueryException $e) {
                if ($e->getCode() === '23000' || Str::contains($e->getMessage(), 'Integrity constraint violation')) {
                    $attempt++;
                    Log::warning("Colisão de código de validação detectada para ave ID {$ave->id}. Tentando novamente. Tentativa: {$attempt}");
                } else {
                    throw $e;
                }
            }
        }

        if (!$saved) {
            return redirect()->back()->with('error', 'Não foi possível gerar um código de validação único para a certidão após várias tentativas. Por favor, tente novamente.');
        }

        return redirect()->route('certidao.show', ['validation_code' => $validationCode])->with('success', 'Certidão emitida com sucesso! O código de validação é: ' . $validationCode);
    }

    /**
     * Exibe a certidão pública usando o código de validação.
     * Esta rota é pública e não requer autenticação.
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
     * @return \Illuminate\View\View
     */
    public function showValidarForm()
    {
        return view('certidao.validar');
    }

    /**
     * Processa a submissão do formulário de validação de certidão.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processValidarForm(Request $request)
    {
        $request->validate([
            'matricula' => 'required|string|max:255',
            'codigo_validacao' => 'required|string|size:11',
        ]);

        $matricula = $request->input('matricula');
        $codigoValidacao = strtoupper($request->input('codigo_validacao'));

        $ave = Ave::withTrashed()
                    ->where('matricula', $matricula)
                    ->where('codigo_validacao_certidao', $codigoValidacao)
                    ->first();

        if ($ave) {
            return redirect()->route('certidao.show', ['validation_code' => $ave->codigo_validacao_certidao])
                             ->with('success', 'Certidão validada com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Matrícula ou Código de Validação inválidos ou não correspondentes.');
        }
    }

    /**
     * Fornece sugestões de pesquisa para aves com base na matrícula e tipo de ave.
     * Retorna uma coleção de objetos JSON contendo 'id', 'matricula', 'tipo_ave_nome' e 'text' (para autocomplete).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
   public function searchSuggestions(Request $request)
    {
        $query = $request->input('query');

        try {
            if (empty($query)) {
                return response()->json([]);
            }

            $aves = Ave::with('tipoAve')
                        ->where('matricula', 'like', '%' . $query . '%')
                        ->orWhereHas('tipoAve', function ($q) use ($query) {
                            $q->where('nome', 'like', '%' . $query . '%');
                        })
                        ->limit(10)
                        ->get();

            $formattedSuggestions = $aves->map(function($ave) {
                $tipoAveNome = $ave->tipoAve->nome ?? 'Tipo Desconhecido';
                return [
                    'id' => $ave->id,
                    'matricula' => $ave->matricula,
                    'tipo_ave_nome' => $tipoAveNome,
                    'text' => "{$ave->matricula} ({$tipoAveNome})", // Adicionado para bibliotecas de autocomplete
                ];
            });

            Log::info('Sugestões de busca retornadas: ' . json_encode($formattedSuggestions->toArray()));
            return response()->json($formattedSuggestions);

        } catch (QueryException $e) {
            Log::error("Erro no banco de dados ao buscar sugestões: " . $e->getMessage() . " - SQL: " . $e->getSql() . " - Bindings: " . json_encode($e->getBindings()));
            return response()->json(['error' => 'Erro interno do servidor ao buscar sugestões.'], 500);
        } catch (\Exception $e) {
            Log::error("Erro geral ao buscar sugestões: " . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor.'], 500);
        }
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

        try {
            $aves = Ave::where('matricula', 'like', '%' . $query . '%')
                       ->orWhereHas('tipoAve', function ($q) use ($query) {
                           $q->where('nome', 'like', '%' . $query . '%');
                       })
                       ->orWhereHas('variacao', function ($q) use ($query) {
                           $q->where('nome', 'like', '%' . $query . '%');
                       })
                       ->with(['tipoAve', 'variacao', 'lote'])
                       ->paginate(10);

            if ($aves->isEmpty()) {
                return redirect()->route('aves.index')->with('error', 'Nenhuma ave encontrada para o termo "' . $query . '".');
            }

            if ($aves->count() === 1 && $aves->first()->matricula === $query) {
                return redirect()->route('aves.show', $aves->first()->id);
            }

            Log::info('Busca de aves realizada com sucesso para: ' . $query);
            return view('aves.listar', compact('aves', 'query'));

        } catch (QueryException $e) {
            Log::error("Erro no banco de dados ao realizar busca de aves: " . $e->getMessage() . " - SQL: " . $e->getSql() . " - Bindings: " . json_encode($e->getBindings()));
            return redirect()->back()->with('error', 'Erro interno do servidor ao realizar a busca.');
        } catch (\Exception $e) {
            Log::error("Erro geral ao realizar busca de aves: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erro interno do servidor.');
        }
    }
}
