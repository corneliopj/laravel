@php
    $pageTitle = 'Listagem de Variações';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')
    {{-- Inclui o partial sidebar --}}
    @include('layouts.partials.sidebar')

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Variações</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Variações</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Todas as Variações</h3>
                                <div class="card-tools">
                                    <a href="{{ route('variacoes.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Variação
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if ($variacoes->isEmpty())
                                    <div class="alert alert-info">
                                        Nenhuma variação cadastrada.
                                    </div>
                                @else
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Tipo de Ave</th>
                                                <th>Ativo</th>
                                                <th style="width: 150px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($variacoes as $variacao)
                                                <tr>
                                                    <td>{{ $variacao->id }}</td>
                                                    <td>{{ $variacao->nome }}</td>
                                                    <td>{{ $variacao->tipoAve->nome ?? 'N/A' }}</td> {{-- Acessando relação tipoAve --}}
                                                    <td>
                                                        @if ($variacao->ativo)
                                                            <span class="badge badge-success">Sim</span>
                                                        @else
                                                            <span class="badge badge-danger">Não</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('variacoes.edit', $variacao->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if ($variacao->ativo)
                                                        <form action="{{ route('variacoes.destroy', $variacao->id) }}" method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Inativar" onclick="return confirm('Tem certeza que deseja inativar esta variação? Isso não será possível se houver aves associadas.');">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('layouts.partials.scripts')
    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
