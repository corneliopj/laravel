@php
    $pageTitle = 'Listagem de Tipos de Aves';
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
                        <h1 class="m-0">Listagem de Tipos de Aves</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Tipos de Aves</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        {{-- Exibe mensagens de sucesso (flash) --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Exibe mensagens de erro (flash) --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="card-body">
                            {{-- Botão Novo Tipo de Ave --}}
                            <div class="mb-3">
                                <a href="{{ route('tipos_aves.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Novo Tipo de Ave
                                </a>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Ativo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Verifica se existem tipos de aves e itera sobre eles --}}
                                    @forelse ($tiposAves as $tipoAve)
                                        <tr>
                                            <td>{{ $tipoAve->id }}</td>
                                            <td>{{ $tipoAve->nome }}</td>
                                            <td>
                                                @if ($tipoAve->ativo)
                                                    <span class="badge bg-success">Sim</span>
                                                @else
                                                    <span class="badge bg-danger">Não</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('tipos_aves.edit', $tipoAve->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                                </a>
                                                {{-- Formulário para inativar (destroy) --}}
                                                <form action="{{ route('tipos_aves.destroy', $tipoAve->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja inativar este tipo de ave? Isso não será possível se houver aves associadas.');">
                                                        <i class="fa-solid fa-trash"></i> Inativar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4">Nenhum tipo de ave cadastrado.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
