<?php

namespace App\Http\Controllers;

use App\Models\Incubacao; // Importa o Model Incubacao
use App\Models\Lote; // Importa o Model Lote para o dropdown
use App\Models\TipoAve; // Importa o Model TipoAve
use App\Models\PosturaOvo; // Importa o Model PosturaOvo
use Illuminate\Http\Request; // Importa a classe Request para lidar com requisições HTTP
use Illuminate\Validation\Rule; // Importa a classe Rule para validação de unicidade
use Carbon\Carbon; // Importa Carbon para manipulação de datas

class IncubacaoController extends Controller
{
    /**
     * Exibe uma lista de todas as incubações.
     */
    public function index()
    {
        // Busca todas as incubações, carregando as relações lote, tipoAve e posturaOvo e ordenando
        $incubacoes = Incubacao::with('lote', 'tipoAve', 'posturaOvo')->orderBy('data_entrada_incubadora', 'desc')->get();

        // Retorna a view de listagem de incubações, passando os dados
        return view('incubacoes.listar', compact('incubacoes'));
    }

    /**
     * Exibe o formulário para criar uma nova incubação.
     */
    public function create()
    {
        // Busca os lotes ativos para o dropdown de lote_ovos_id
        $lotes = Lote::where('ativo', 1)->get();
        // Busca todos os tipos de aves para o dropdown de tipo_ave_id
        $tiposAves = TipoAve::all();
        // CORREÇÃO: Busca TODAS as posturas de ovos para o dropdown de postura_ovo_id
        // Se a intenção for apenas posturas que NÃO geraram uma incubação ainda, a lógica precisará ser mais complexa.
        // Por enquanto, vamos listar todas para que você possa ver as opções.
        $posturasOvos = PosturaOvo::with(['acasalamento.macho', 'acasalamento.femea'])->get();


        // Opções para o campo 'chocadeira'
        $chocadeiras = ['130-1', '130-2', '130-3', 'Caixa', 'Garnisé', 'Perua', 'Galinha'];

        return view('incubacoes.criar', compact('lotes', 'tiposAves', 'posturasOvos', 'chocadeiras'));
    }

    /**
     * Armazena uma nova incubação no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'lote_ovos_id' => 'required|exists:lotes,id',
            'tipo_ave_id' => 'required|exists:tipos_aves,id',
            'data_entrada_incubadora' => 'required|date',
            'quantidade_ovos' => 'required|integer|min:1',
            'postura_ovo_id' => 'nullable|exists:posturas_ovos,id', // postura_ovo_id é opcional
            'chocadeira' => 'nullable|in:130-1,130-2,130-3,Caixa,Garnisé,Perua,Galinha', // Validação para chocadeira
            'quantidade_inferteis' => 'nullable|integer|min:0', // Validação para ovos inférteis
            'quantidade_infectados' => 'nullable|integer|min:0', // Validação para ovos infectados
            'quantidade_mortos' => 'nullable|integer|min:0',     // Validação para ovos mortos
            'observacoes' => 'nullable|string|max:500',
        ]);

        try {
            // Calcula a data prevista de eclosão com base no tempo_eclosao do TipoAve
            $tipoAve = TipoAve::findOrFail($request->tipo_ave_id);
            $dataPrevistaEclosao = Carbon::parse($request->data_entrada_incubadora)
                                        ->addDays($tipoAve->tempo_eclosao);

            Incubacao::create([
                'lote_ovos_id' => $request->lote_ovos_id,
                'tipo_ave_id' => $request->tipo_ave_id,
                'data_entrada_incubadora' => $request->data_entrada_incubadora,
                'data_prevista_eclosao' => $dataPrevistaEclosao, // Usando a data calculada
                'quantidade_ovos' => $request->quantidade_ovos,
                'quantidade_eclodidos' => 0, // Inicializa como 0
                'observacoes' => $request->observacoes,
                'ativo' => 1, // Ativa por padrão
                'postura_ovo_id' => $request->postura_ovo_id, // Armazena postura_ovo_id
                'chocadeira' => $request->chocadeira,             // Armazena chocadeira
                'quantidade_inferteis' => $request->quantidade_inferteis ?? 0, // Armazena ovos inférteis
                'quantidade_infectados' => $request->quantidade_infectados ?? 0, // Armazena ovos infectados
                'quantidade_mortos' => $request->quantidade_mortos ?? 0,     // Armazena ovos mortos
            ]);

            return redirect()->route('incubacoes.index')->with('success', 'Incubação criada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar incubação: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao criar a incubação: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar uma incubação existente.
     */
    public function edit(string $id)
    {
        $incubacao = Incubacao::with('lote', 'tipoAve', 'posturaOvo')->findOrFail($id); // Carrega relações
        $lotes = Lote::where('ativo', 1)->get();
        $tiposAves = TipoAve::all();
        // CORREÇÃO: Busca TODAS as posturas de ovos para o dropdown de postura_ovo_id
        $posturasOvos = PosturaOvo::with(['acasalamento.macho', 'acasalamento.femea'])->get();

        // Opções para o campo 'chocadeira'
        $chocadeiras = ['130-1', '130-2', '130-3', 'Caixa', 'Garnisé', 'Perua', 'Galinha'];

        return view('incubacoes.editar', compact('incubacao', 'lotes', 'tiposAves', 'posturasOvos', 'chocadeiras'));
    }

    /**
     * Atualiza uma incubação existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $incubacao = Incubacao::findOrFail($id);

        // Validação dos dados de entrada
        $request->validate([
            'lote_ovos_id' => 'required|exists:lotes,id',
            'tipo_ave_id' => 'required|exists:tipos_aves,id',
            'data_entrada_incubadora' => 'required|date',
            'quantidade_ovos' => 'required|integer|min:1',
            'quantidade_eclodidos' => 'required|integer|min:0|max:' . $request->quantidade_ovos, // Garante que não excede o total
            'postura_ovo_id' => 'nullable|exists:posturas_ovos,id', // postura_ovo_id é opcional
            'chocadeira' => 'nullable|in:130-1,130-2,130-3,Caixa,Garnisé,Perua,Galinha', // Validação para chocadeira
            'quantidade_inferteis' => 'nullable|integer|min:0', // Validação para ovos inférteis
            'quantidade_infectados' => 'nullable|integer|min:0', // Validação para ovos infectados
            'quantidade_mortos' => 'nullable|integer|min:0',     // Validação para ovos mortos
            'observacoes' => 'nullable|string|max:500',
        ]);

        try {
            // Calcula a data prevista de eclosão com base no tempo_eclosao do TipoAve
            $tipoAve = TipoAve::findOrFail($request->tipo_ave_id);
            $dataPrevistaEclosao = Carbon::parse($request->data_entrada_incubadora)
                                        ->addDays($tipoAve->tempo_eclosao);

            $incubacao->update([
                'lote_ovos_id' => $request->lote_ovos_id,
                'tipo_ave_id' => $request->tipo_ave_id,
                'data_entrada_incubadora' => $request->data_entrada_incubadora,
                'data_prevista_eclosao' => $dataPrevistaEclosao, // Usando a data calculada
                'quantidade_ovos' => $request->quantidade_ovos,
                'quantidade_eclodidos' => $request->quantidade_eclodidos,
                'observacoes' => $request->observacoes,
                'ativo' => $request->has('ativo') ? 1 : 0, // Mantém a lógica de ativação/inativação
                'postura_ovo_id' => $request->postura_ovo_id, // Atualiza postura_ovo_id
                'chocadeira' => $request->chocadeira,             // Atualiza chocadeira
                'quantidade_inferteis' => $request->quantidade_inferteis ?? 0, // Atualiza ovos inférteis
                'quantidade_infectados' => $request->quantidade_infectados ?? 0, // Atualiza ovos infectados
                'quantidade_mortos' => $request->quantidade_mortos ?? 0,     // Atualiza ovos mortos
            ]);

            return redirect()->route('incubacoes.index')->with('success', 'Incubação atualizada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar incubação: ' . $e->getMessage(), ['incubacao_id' => $id, 'request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar a incubação: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de uma incubação específica.
     */
    public function show(string $id)
    {
        $incubacao = Incubacao::with(['lote', 'tipoAve', 'posturaOvo', 'aves.tipoAve', 'aves.variacao'])->findOrFail($id);

        return view('incubacoes.ficha', compact('incubacao'));
    }

    /**
     * Inativa uma incubação do banco de dados (inativação lógica).
     */
    public function destroy(string $id)
    {
        try {
            $incubacao = Incubacao::find($id);

            if (!$incubacao) {
                return redirect()->route('incubacoes.index')->with('error', 'Incubação não encontrada para inativação.');
            }

            // Verifica se há aves associadas a esta incubação antes de inativar
            if ($incubacao->aves()->count() > 0) {
                return redirect()->route('incubacoes.index')->with('error', 'Não é possível inativar esta incubação, pois existem aves associadas a ela.');
            }

            // Inativação lógica da incubação
            $incubacao->update([
                'ativo' => 0,
            ]);

            return redirect()->route('incubacoes.index')->with('success', 'Incubação inativada com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao inativar incubação: ' . $e->getMessage(), ['incubacao_id' => $id]);
            return redirect()->route('incubacoes.index')->with('error', 'Erro ao inativar a incubação.');
        }
    }
}
