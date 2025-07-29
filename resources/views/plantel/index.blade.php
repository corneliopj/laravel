@php
    $pageTitle = 'Listagem de Plantéis';
@endphp

@include('layouts.partials.head')

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Listagem de Plantéis</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Plantéis</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Plantéis Cadastrados</h3>
                                <div class="card-tools">
                                    <a href="{{ route('plantel.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Novo Plantel
                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Identificação do Grupo</th>
                                                <th>Tipo de Ave</th>
                                                <th>Data de Formação</th>
                                                <th>Qtd. Inicial</th>
                                                <th>Qtd. Atual</th>
                                                <th>Ativo</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($plantelData as $plantel)
                                                <tr>
                                                    <td>{{ $plantel['id'] }}</td>
                                                    <td>{{ $plantel['identificacao_grupo'] }}</td>
                                                    <td>{{ $plantel['tipo_ave_nome'] }}</td>
                                                    <td>{{ $plantel['data_formacao'] }}</td>
                                                    <td>{{ $plantel['quantidade_inicial'] }}</td>
                                                    <td>{{ $plantel['quantidade_atual'] }}</td>
                                                    <td>
                                                        @if ($plantel['ativo'])
                                                            <span class="badge badge-success">Sim</span>
                                                        @else
                                                            <span class="badge badge-danger">Não</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ $plantel['link_detalhes'] }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ $plantel['link_editar'] }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('plantel.destroy', $plantel['id']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este plantel? Todas as movimentações associadas serão removidas.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">Nenhum plantel cadastrado.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    @include('layouts.partials.scripts')
    @include('layouts.partials.footer')
</div>
<!-- ./wrapper -->
