<?php

namespace App\Http\Controllers;

use App\Models\TipoAve; // Certifique-se de que o modelo TipoAve está importado
use Illuminate\Http\Request;

class TipoAveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiposAves = TipoAve::orderBy('nome')->paginate(10);
        return view('tipos_aves.index', compact('tiposAves'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipos_aves.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:tipos_aves,nome',
            'descricao' => 'nullable|string|max:1000',
            'tempo_eclosao' => 'required|integer|min:1', // Adicionado validação
        ]);

        TipoAve::create($request->all());
        return redirect()->route('tipos_aves.index')->with('success', 'Tipo de Ave criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoAve $tipoAve)
    {
        return view('tipos_aves.show', compact('tipoAve'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoAve $tipoAve)
    {
        return view('tipos_aves.edit', compact('tipoAve'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoAve $tipoAve)
    {
        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tipos_aves')->ignore($tipoAve->id),
            ],
            'descricao' => 'nullable|string|max:1000',
            'tempo_eclosao' => 'required|integer|min:1', // Adicionado validação
        ]);

        $tipoAve->update($request->all());
        return redirect()->route('tipos_aves.index')->with('success', 'Tipo de Ave atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoAve $tipoAve)
    {
        try {
            $tipoAve->delete();
            return redirect()->route('tipos_aves.index')->with('success', 'Tipo de Ave excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('tipos_aves.index')->with('error', 'Erro ao excluir Tipo de Ave: ' . $e->getMessage());
        }
    }

    /**
     * NOVO MÉTODO: Retorna o tempo de eclosão de um tipo de ave específico via AJAX.
     *
     * @param  \App\Models\TipoAve  $tipoAve
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTempoEclosao(TipoAve $tipoAve)
    {
        return response()->json(['tempo_eclosao' => $tipoAve->tempo_eclosao]);
    }
}
