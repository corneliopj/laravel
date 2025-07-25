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
use Illuminate\Support\Facades\Log; // Importar a facade Log

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
            'sexo' => ['required', Rule::in(['Macho', 'Fêmea', 'Indefinido'])],
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
            'sexo' => ['required', Rule::in(['Macho', 'Fêmea', 'Indefinido'])],
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
                // Gera um novo código XXXXX-YYYYY em cada tentativa de colisão.
                // O loop interno do `do-while` verifica a unicidade ANTES de tentar salvar.
                // A condição $attempt < $maxRetries no do-while é para evitar loop infinito em caso de azar extremo.
                do {
                    $hexPart = bin2hex(random_bytes(5)); // Gera 5 bytes aleatórios (10 caracteres hexadecimais)
                    $code = strtoupper(substr($hexPart, 0, 5) . '-' . substr($hexPart, 5, 5)); // Formata XXXXX-YYYYY
                } while (Ave::where('codigo_validacao_certidao', $code)->exists() && $attempt < $maxRetries);

                // Se o loop interno de geração de código excedeu as tentativas (improvável), sai do loop externo.
                if ($attempt >= $maxRetries) {
                    break;
                }

                $validationCode = $code; // Define o código gerado para uso posterior
                $ave->codigo_validacao_certidao = $validationCode;
                $ave->save(); // Tenta salvar a ave. Se o código for duplicado aqui, QueryException será lançada.

                $saved = true; // Marca como salvo se o save() foi bem-sucedido
            } catch (QueryException $e) {
                // Captura a exceção de banco de dados.
                // Verifica se é uma violação de unicidade (código '23000' para MySQL/MariaDB).
                if ($e->getCode() === '23000' || Str::contains($e->getMessage(), 'Integrity constraint violation')) {
                    $attempt++; // Incrementa as tentativas
                    Log::warning("Colisão de código de validação detectada para ave ID {$ave->id}. Tentando novamente. Tentativa: {$attempt}");
                    // Opcional: usleep(100000); // Pequena pausa para evitar sobrecarga em caso de muitas colisões rápidas (muito raro)
                } else {
                    // Se for outro tipo de erro de banco de dados, relança a exceção
                    throw $e;
                }
            }
        }

        if (!$saved) {
            // Se o código não pôde ser salvo após todas as tentativas, redireciona com erro.
            return redirect()->back()->with('error', 'Não foi possível gerar um código de validação único para a certidão após várias tentativas. Por favor, tente novamente.');
        }

        // Redireciona para a rota pública da certidão com o código de validação gerado
        return redirect()->route('certidao.show', ['validation_code' => $validationCode])->with('success', 'Certidão emitida com sucesso! O código de validação é: ' . $validationCode);
    }

    /**
     * Exibe a certidão pública usando o código de validação.
     * Esta rota é pública e não requer autenticação.
     */
    public function showCertidao(string $validation_code)
    {
        // DEBUG: Verifica se o método está sendo chamado
        // dd('Chegou em AveController@showCertidao com código: ' . $validation_code);

        $validation_code = strtoupper($validation_code); // Converte para maiúsculas para corresponder ao DB

        // Busca a ave pelo código de validação.
        // Inclui aves soft-deletadas, pois uma certidão pode ser de uma ave "morta" ou "inativada".
        $ave = Ave::withTrashed()->with('mortes')->where('codigo_validacao_certidao', $validation_code)->first();

        if (!$ave) {
            // Se a ave não for encontrada, redireciona para a home com mensagem de erro.
            return redirect('/')->with('error', 'Certidão não encontrada ou código de validação inválido.');
        }

        // Carrega as relações adicionais necessárias para a view da certidão
        $ave->load(['tipoAve', 'variacao', 'lote', 'incubacao.posturaOvo.acasalamento.macho', 'incubacao.posturaOvo.acasalamento.femea']);

        // Retorna a view da certidão com os dados da ave
        return view('certidao.show', compact('ave'));
    }

    /**
     * Exibe o formulário de validação de certidão para usuários externos.
     * @return \Illuminate\View\View
     */
    public function showValidarForm()
    {
        // DEBUG: Verifica se o método está sendo chamado
        //dd('Chegou em AveController@showValidarForm (GET para /certidao/validar)');
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
        // DEBUG: Verifica se o método POST está sendo chamado
        //dd('Chegou em AveController@processValidarForm (POST para /certidao/validar)', $request->all());

        // Valida os campos do formulário
        $request->validate([
            'matricula' => 'required|string|max:255',
            'codigo_validacao' => 'required|string|size:11', // O código XXXXX-YYYYY tem 11 caracteres
        ]);

        $matricula = $request->input('matricula');
        $codigoValidacao = strtoupper($request->input('codigo_validacao')); // Converte para maiúsculas

        // Busca a ave que corresponde à matrícula E ao código de validação
        $ave = Ave::withTrashed() // Inclui aves soft-deletadas, pois a certidão pode ser de uma ave "morta"
                    ->where('matricula', $matricula)
                    ->where('codigo_validacao_certidao', $codigoValidacao)
                    ->first();

        if ($ave) {
            // Se encontrar a ave, redireciona para a rota pública da certidão
            return redirect()->route('certidao.show', ['validation_code' => $ave->codigo_validacao_certidao])
                             ->with('success', 'Certidão validada com sucesso!');
        } else {
            // Se não encontrar, redireciona de volta com erro e mantem os inputs para o usuário corrigir
            return redirect()->back()->withInput()->with('error', 'Matrícula ou Código de Validação inválidos ou não correspondentes.');
        }
    }

    /**
     * Retorna sugestões de matrícula para o campo de busca.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('term'); // 'term' é o parâmetro padrão do jQuery UI Autocomplete
        $suggestions = Ave::where('matricula', 'like', '%' . $query . '%')
                          ->limit(10) // Limita o número de sugestões
                          ->pluck('matricula'); // Retorna apenas as matrículas

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

        // Se houver apenas um resultado e a matrícula corresponder exatamente, redireciona para a ficha
        if ($aves->count() === 1 && $aves->first()->matricula === $query) {
            return redirect()->route('aves.show', $aves->first()->id);
        }

        return view('aves.listar', compact('aves'));
    }
}
