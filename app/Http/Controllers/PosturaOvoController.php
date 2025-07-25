<?php

namespace App\Http\Controllers;

use App\Models\PosturaOvo;
use App\Models\Acasalamento;
use App\Models\Incubacao; // Importa o Model Incubacao
use App\Models\TipoAve;    // Importa o Model TipoAve
use App\Models\Lote;      // NOVO: Importa o Model Lote
use Illuminate\Http\Request; // Importa a classe Request para lidar com requisições HTTP
use Illuminate\Validation\Rule; // Importa a classe Rule para validação de unicidade
use Carbon\Carbon; // Para manipulação de datas

class PosturaOvoController extends Controller
{
    /**
     * Exibe uma lista de todas as posturas de ovos.
     * Pode receber um acasalamento_id para filtrar.
     */
    public function index(Request $request)
    {
        $acasalamentoId = $request->query('acasalamento_id');

        $query = PosturaOvo::with(['acasalamento.macho', 'acasalamento.femea'])
                            ->orderBy('data_inicio_postura', 'desc');

        if ($acasalamentoId) {
            $query->where('acasalamento_id', $acasalamentoId);
        }

        $posturasOvos = $query->get();
        $acasalamentos = Acasalamento::with(['macho', 'femea'])->get(); // Para o filtro na view

        return view('posturas_ovos.listar', compact('posturasOvos', 'acasalamentoId', 'acasalamentos'));
    }

    /**
     * Exibe o formulário para criar uma nova postura de ovos.
     * Esta função será usada para iniciar uma nova "postura" (período).
     */
    public function create()
    {
        // Busca acasalamentos que NÃO POSSUEM uma postura de ovos ATIVA (encerrada = 0)
        $acasalamentos = Acasalamento::with(['macho', 'femea'])
            ->whereDoesntHave('posturasOvos', function ($query) {
                $query->where('encerrada', 0); // Onde 'encerrada' é false (ou 0)
            })
            ->get();

        return view('posturas_ovos.criar', compact('acasalamentos'));
    }

    /**
     * Armazena um novo acasalamento no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'acasalamento_id' => [
                'required',
                'exists:acasalamentos,id',
                // Garante que não existe outra postura de ovos ATIVA para este acasalamento
                Rule::unique('posturas_ovos')->where(function ($query) {
                    return $query->where('encerrada', 0); // Onde 'encerrada' é false (ou 0)
                }),
            ],
            'data_inicio_postura' => 'required|date',
            'data_fim_postura' => 'nullable|date|after_or_equal:data_inicio_postura',
            'quantidade_ovos' => 'required|integer|min:0',
            'observacoes' => 'nullable|string',
        ], [
            'acasalamento_id.required' => 'O acasalamento é obrigatório.',
            'acasalamento_id.exists' => 'O acasalamento selecionado é inválido.',
            'acasalamento_id.unique' => 'Já existe uma postura de ovos ATIVA para este acasalamento. Encerre a postura anterior antes de criar uma nova.',
            'data_inicio_postura.required' => 'A data de início da postura é obrigatória.',
            'data_inicio_postura.date' => 'A data de início da postura deve ser uma data válida.',
            'data_fim_postura.after_or_equal' => 'A data de fim da postura não pode ser anterior à data de início.',
            'quantidade_ovos.required' => 'A quantidade de ovos é obrigatória.',
            'quantidade_ovos.integer' => 'A quantidade de ovos deve ser um número inteiro.',
            'quantidade_ovos.min' => 'A quantidade de ovos deve ser no mínimo 0.',
        ]);

        try {
            PosturaOvo::create($request->all());
            return redirect()->route('posturas_ovos.index')->with('success', 'Postura de ovos registada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao registar postura de ovos: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao registar a postura de ovos: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar uma postura de ovos existente.
     */
    public function edit(string $id)
    {
        $posturaOvo = PosturaOvo::findOrFail($id); // Use findOrFail para lançar 404 se não encontrar

        // Ao editar, o acasalamento não pode ser alterado, então buscamos apenas o acasalamento atual
        // para garantir que ele seja exibido no select, mesmo que não esteja "disponível" para novas posturas.
        $acasalamentos = Acasalamento::with(['macho', 'femea'])->where('id', $posturaOvo->acasalamento_id)->get();

        return view('posturas_ovos.editar', compact('posturaOvo', 'acasalamentos'));
    }

    /**
     * Atualiza uma postura de ovos existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $posturaOvo = PosturaOvo::findOrFail($id); // Use findOrFail

        $request->validate([
            'acasalamento_id' => [
                'required',
                'exists:acasalamentos,id',
                // Garante que não existe outra postura de ovos ATIVA para este acasalamento,
                // ignorando a própria postura que está sendo atualizada.
                Rule::unique('posturas_ovos')->ignore($posturaOvo->id)->where(function ($query) {
                    return $query->where('encerrada', 0); // Onde 'encerrada' é false (ou 0)
                }),
            ],
            'data_inicio_postura' => 'required|date',
            'data_fim_postura' => 'nullable|date|after_or_equal:data_inicio_postura',
            'quantidade_ovos' => 'required|integer|min:0',
            'observacoes' => 'nullable|string',
            'encerrada' => 'boolean', // Certifique-se que este campo é validado se estiver no formulário
        ], [
            'acasalamento_id.required' => 'O acasalamento é obrigatório.',
            'acasalamento_id.exists' => 'O acasalamento selecionado é inválido.',
            'acasalamento_id.unique' => 'Já existe uma postura de ovos ATIVA para este acasalamento. Encerre a postura anterior antes de criar uma nova.',
            'data_inicio_postura.required' => 'A data de início da postura é obrigatória.',
            'data_inicio_postura.date' => 'A data de início da postura deve ser uma data válida.',
            'data_fim_postura.after_or_equal' => 'A data de fim da postura não pode ser anterior à data de início.',
            'quantidade_ovos.required' => 'A quantidade de ovos é obrigatória.',
            'quantidade_ovos.integer' => 'A quantidade de ovos deve ser um número inteiro.',
            'quantidade_ovos.min' => 'A quantidade de ovos deve ser no mínimo 0.',
        ]);

        try {
            $posturaOvo->update($request->all());
            return redirect()->route('posturas_ovos.index')->with('success', 'Postura de ovos atualizada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar postura de ovos: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar a postura de ovos: ' . $e->getMessage());
        }
    }

    /**
     * Remove uma postura de ovos do banco de dados.
     */
    public function destroy(string $id)
    {
        try {
            $posturaOvo = PosturaOvo::findOrFail($id); // Use findOrFail

            // Impede a exclusão se a postura já deu origem a uma incubação
            if ($posturaOvo->incubacoes()->count() > 0) {
                return redirect()->route('posturas_ovos.index')->with('error', 'Não é possível excluir esta postura, pois já gerou uma incubação.');
            }

            $posturaOvo->delete();
            return redirect()->route('posturas_ovos.index')->with('success', 'Postura de ovos excluída com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir postura de ovos: ' . $e->getMessage(), ['postura_ovo_id' => $id]);
            return redirect()->route('posturas_ovos.index')->with('error', 'Erro ao excluir a postura de ovos.');
        }
    }

    /**
     * Incrementa a quantidade de ovos para uma postura específica.
     * Esta será a ação chamada pelo botão '+' na interface.
     */
    public function incrementOvos(Request $request, string $id)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1', // Quantidade a ser adicionada
        ], [
            'quantidade.required' => 'A quantidade a adicionar é obrigatória.',
            'quantidade.min' => 'A quantidade a adicionar deve ser no mínimo 1.',
        ]);

        try {
            $posturaOvo = PosturaOvo::findOrFail($id); // Use findOrFail

            if ($posturaOvo->encerrada) { // Verifica o novo campo 'encerrada'
                return response()->json(['error' => 'Não é possível adicionar ovos a uma postura encerrada.'], 400);
            }

            $posturaOvo->increment('quantidade_ovos', $request->quantidade); // Usando increment para atomicidade

            return response()->json(['success' => 'Quantidade de ovos incrementada com sucesso!', 'new_quantity' => $posturaOvo->quantidade_ovos]);
        } catch (\Exception $e) {
            \Log::error('Erro ao incrementar ovos: ' . $e->getMessage(), ['postura_ovo_id' => $id, 'request_data' => $request->all()]);
            return response()->json(['error' => 'Erro ao incrementar ovos: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Encerra uma postura de ovos e inicia um processo de incubação.
     */
    public function encerrarPostura(Request $request, string $id)
    {
        // Validação dos dados da requisição para o encerramento
        $validator = \Validator::make($request->all(), [
            'data_fim_postura' => 'required|date',
        ], [
            'data_fim_postura.required' => 'A data de fim da postura é obrigatória.',
            'data_fim_postura.date' => 'A data de fim da postura deve ser uma data válida.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422); // Retorna o primeiro erro de validação
        }

        try {
            $posturaOvo = PosturaOvo::with('acasalamento.macho.tipoAve')->findOrFail($id); // Use findOrFail

            if ($posturaOvo->encerrada) { // Verifica o novo campo 'encerrada'
                return response()->json(['error' => 'Esta postura já foi encerrada.'], 400);
            }

            // 1. Atualiza a data de fim da postura E DEFINE 'encerrada' como true
            $posturaOvo->data_fim_postura = $request->data_fim_postura;
            $posturaOvo->encerrada = true; // Define como encerrada
            $posturaOvo->save();

            // 2. Cria ou encontra o Lote de Ovos com a identificação "POSTXXXX"
            $identificacaoLote = 'POST' . str_pad($posturaOvo->id, 4, '0', STR_PAD_LEFT);

            $loteOvos = Lote::firstOrCreate(
                ['identificacao_lote' => $identificacaoLote],
                ['observacoes' => 'Lote gerado automaticamente do encerramento da postura ID: ' . $posturaOvo->id, 'ativo' => 1]
            );

            // 3. Cria o registro de Incubação
            $tipoAve = $posturaOvo->acasalamento->macho->tipoAve; // Assume que o tipo de ave é o mesmo do macho
            
            if (!$tipoAve || !isset($tipoAve->tempo_eclosao)) {
                return response()->json(['error' => 'Não foi possível iniciar a incubação: Tempo de eclosão não definido para o tipo de ave.'], 400);
            }

            $dataEntradaIncubadora = Carbon::parse($request->data_fim_postura);
            $dataPrevistaEclosao = $dataEntradaIncubadora->copy()->addDays($tipoAve->tempo_eclosao);

            Incubacao::create([
                'lote_ovos_id' => $loteOvos->id, // Usa o ID do lote recém-criado/encontrado
                'tipo_ave_id' => $tipoAve->id,
                'data_entrada_incubadora' => $dataEntradaIncubadora,
                'data_prevista_eclosao' => $dataPrevistaEclosao,
                'quantidade_ovos' => $posturaOvo->quantidade_ovos,
                'quantidade_eclodidos' => 0, // Inicialmente 0
                'observacoes' => 'Incubação gerada automaticamente do encerramento da postura ID: ' . $posturaOvo->id,
                'ativo' => 1,
                'postura_ovo_id' => $posturaOvo->id,
            ]);

            return response()->json(['success' => 'Postura encerrada e incubação iniciada com sucesso!'], 200);

        } catch (\Exception $e) {
            \Log::error('Erro ao encerrar postura ou iniciar incubação: ' . $e->getMessage(), ['postura_ovo_id' => $id, 'request_data' => $request->all()]);
            return response()->json(['error' => 'Erro ao encerrar a postura e iniciar a incubação: ' . $e->getMessage()], 500);
        }
    }
}
