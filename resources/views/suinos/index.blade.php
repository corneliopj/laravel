@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h2>Gestão de Suínos</h2>
            <a href="{{ route('suinos.create') }}" class="btn btn-primary">Novo Suíno</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Sexo</th>
                        <th>Vendável</th>
                        <th>Ativo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suinos as $suino)
                    <tr>
                        <td>{{ $suino->matricula }}</td>
                        <td>{{ $suino->sexo }}</td>
                        <td>{{ $suino->vendavel ? 'Sim' : 'Não' }}</td>
                        <td>{{ $suino->ativo ? 'Ativo' : 'Inativo' }}</td>
                        <td>
                            <a href="{{ route('suinos.edit', $suino->id) }}" class="btn btn-sm btn-info">Editar</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $suinos->links() }}
        </div>
    </div>
</div>
@endsection
