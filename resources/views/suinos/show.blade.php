@extends('layouts.app')

@section('title', 'Detalhes do Suíno')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detalhes do Suíno: {{ $suino->matricula }}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('suinos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="{{ route('suinos.edit', $suino->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Card de Informações Principais -->
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Informações Gerais</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%"><strong>Matrícula:</strong></th>
                                <td>{{ $suino->matricula }}</td>
                            </tr>
                            <tr>
                                <th><strong>Tipo:</strong></th>
                                <td>
                                    @if($suino->tipo == 'matriz')
                                        <span class="badge badge-success">Matriz</span>
                                    @else
                                        <span class="badge badge-info">Transitório</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><strong>Sexo:</strong></th>
                                <td>{{ $suino->sexo ?? 'Não informado' }}</td>
                            </tr>
                            <tr>
                                <th><strong>Vendável:</strong></th>
                                <td>
                                    {!! $suino->vendavel ? '<span class="text-success">Sim</span>' : '<span class="text-danger">Não</span>' !!}
                                </td>
                            </tr>
                            <tr>
                                <th><strong>Status:</strong></th>
                                <td>
                                    @if($suino->ativo)
                                        <span class="badge badge-success">Ativo</span>
                                    @else
                                        <span class="badge badge-danger">Inativo</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card de Vínculos -->
            <div class="col-md-6">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Vínculos de Manejo</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%"><strong>Lote:</strong></th>
                                <td>{{ $suino->lote->identificacao_lote ?? 'Nenhum lote atribuído' }}</td>
                            </tr>
                            <tr>
                                <th><strong>Variação:</strong></th>
                                <td>{{ $suino->variacao->nome ?? 'Nenhuma variação atribuída' }}</td>
                            </tr>
                            <tr>
                                <th><strong>Descrição Variação:</strong></th>
                                <td>{{ $suino->variacao->descricao ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Histórico de Mortes -->
            <div class="col-12">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Registro de Morte / Ocorrências</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Causa</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suino->mortes as $morte)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($morte->data_morte)->format('d/m/Y') }}</td>
                                        <td>{{ $morte->causa ?? 'Não informada' }}</td>
                                        <td>{{ $morte->observacoes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Nenhum registro de morte encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
