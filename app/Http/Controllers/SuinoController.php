<?php

namespace App\Http\Controllers;

use App\Models\Suino;
use Illuminate\Http\Request;

class SuinoController extends Controller
{
    public function index()
    {
        $suinos = Suino::with(['lote', 'variacao'])->paginate(10);
        return view('suinos.index', compact('suinos'));
    }

    public function create()
    {
        $lotes = \App\Models\Lote::all();
        $variacoes = \App\Models\Variacao::all();
        return view('suinos.create', compact('lotes', 'variacoes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricula' => 'required|unique:suinos',
            'tipo' => 'required|in:matriz,transitorio',
            'lote_id' => 'nullable|exists:lotes,id',
            'variacao_id' => 'nullable|exists:variacoes,id',
            'sexo' => 'required',
            'vendavel' => 'boolean',
        ]);

        Suino::create($validated);

        return redirect()->route('suinos.index')->with('success', 'Suíno cadastrado com sucesso!');
    }

    public function show(Suino $suino)
    {
        // Carrega os relacionamentos para a view de detalhes
        $suino->load(['lote', 'variacao', 'mortes']);
        return view('suinos.show', compact('suino'));
    }

    public function edit(Suino $suino)
    {
        $lotes = \App\Models\Lote::all();
        $variacoes = \App\Models\Variacao::all();
        return view('suinos.edit', compact('suino', 'lotes', 'variacoes'));
    }

    public function update(Request $request, Suino $suino)
    {
        $validated = $request->validate([
            'matricula' => 'required|unique:suinos,matricula,' . $suino->id,
            'tipo' => 'required|in:matriz,transitorio',
            'lote_id' => 'nullable|exists:lotes,id',
            'variacao_id' => 'nullable|exists:variacoes,id',
            'sexo' => 'required',
            'vendavel' => 'boolean',
        ]);

        $suino->update($validated);

        return redirect()->route('suinos.index')->with('success', 'Suíno atualizado com sucesso!');
    }

    public function destroy(Suino $suino)
    {
        $suino->delete();
        return redirect()->route('suinos.index')->with('success', 'Suíno removido com sucesso!');
    }
}
