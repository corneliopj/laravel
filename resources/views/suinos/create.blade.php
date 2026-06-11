@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Cadastrar Suíno</h2>
    <form action="{{ route('suinos.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label>Matrícula</label>
            <input type="text" name="matricula" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label>Sexo</label>
            <select name="sexo" class="form-control">
                <option value="Macho">Macho</option>
                <option value="Femea">Femea</option>
                <option value="A sexar">A sexar</option>
            </select>
        </div>
        <div class="form-group mb-3">
            <label>Vendável</label>
            <div class="form-check">
                <input type="checkbox" name="vendavel" value="1" class="form-check-input" id="vendavel">
                <label class="form-check-label" for="vendavel">Pronto para venda</label>
            </div>
        </div>
        <div class="form-group mb-3">
            <label>Ativo</label>
            <div class="form-check">
                <input type="checkbox" name="ativo" value="1" class="form-check-input" checked id="ativo">
                <label class="form-check-label" for="ativo">Ativo</label>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('suinos.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection
