<?php

namespace App\Http\Controllers;

use App\Models\Suino;
use Illuminate\Http\Request;

class SuinoController extends Controller
{
    public function index()
    {
        $suinos = Suino::orderBy('created_at', 'desc')->paginate(10);
        return view('suinos.index', compact('suinos'));
    }

    public function create()
    {
        return view('suinos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'matricula' => 'required|unique:suinos,matricula',
            'sexo' => 'required|in:Macho,Femea,A sexar',
            'vendavel' => 'boolean',
            'ativo' => 'boolean',
        ]);

        Suino::create($request->all());
        return redirect()->route('suinos.index')->with('success', 'Suíno cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $suino = Suino::findOrFail($id);
        return view('suinos.edit', compact('suino'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'matricula' => 'required|unique:suinos,matricula,' . $id,
            'sexo' => 'required|in:Macho,Femea,A sexar',
            'vendavel' => 'boolean',
            'ativo' => 'boolean',
        ]);

        $suino = Suino::findOrFail($id);
        $suino->update($request->all());
        return redirect()->route('suinos.index')->with('success', 'Suíno atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $suino = Suino::findOrFail($id);
        $suino->update(['ativo' => 0]);
        return redirect()->route('suinos.index')->with('success', 'Suíno inativado com sucesso!');
    }
}
