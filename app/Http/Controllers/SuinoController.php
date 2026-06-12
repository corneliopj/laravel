<?php

namespace App\Http\Controllers;

use App\Models\Suino;
use Illuminate\Http\Request;

class SuinoController extends Controller
{
    public function index()
    {
        $suinos = Suino::paginate(10);
        return view('suinos.index', compact('suinos'));
    }

    public function create()
    {
        return view('suinos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricula' => 'required|unique:suinos',
            'sexo' => 'required',
            'vendavel' => 'boolean',
        ]);

        Suino::create($validated);

        return redirect()->route('suinos.index')->with('success', 'Suíno cadastrado com sucesso!');
    }

    public function edit(Suino $suino)
    {
        return view('suinos.edit', compact('suino'));
    }

    public function update(Request $request, Suino $suino)
    {
        $validated = $request->validate([
            'matricula' => 'required|unique:suinos,matricula,' . $suino->id,
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
