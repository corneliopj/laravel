@php
    $pageTitle = 'Listagem de Mortes';
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
                        <h1>Listagem de Mortes</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Mortes</li>
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
                                <h3 class="card-title">Registros de Morte</h3>
                                <div class="card-tools">
                                    <a href="{{ route('mortes.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Registrar Morte
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
                                                <th>Tipo</th>
                                                <th>Identificação</th>
                                                <th>Quantidade</th>
                                                <th>Data da Morte</th>
                                                <th>Causa</th>
                                                <th>Observações</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($mortes as $morte)
                                                <tr>
                                                    <td>{{ $morte->id }}</td>
                                                    <td>
                                                        @if ($morte->ave_id)
                                                            Individual
                                                        @elseif ($morte->plantel_id)
                                                            Plantel
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($morte->ave_id)
                                                            <a href="{{ route('aves.show', $morte->ave_id) }}">{{ $morte->ave->matricula ?? 'Ave Removida' }}</a>
                                                        @elseif ($morte->plantel_id)
                                                            <a href="{{ route('plantel.show', $morte->plantel_id) }}">{{ $morte->plantel->identificacao_grupo ?? 'Plantel Removido' }}</a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ $morte->quantidade_mortes_plantel ?? 1 }}</td> {{-- Se individual, é 1 --}}
                                                    <td>{{ $morte->data_morte->format('d/m/Y') }}</td>
                                                    <td>{{ $morte->causa_morte ?? 'Não informada' }}</td>
                                                    <td>{{ $morte->observacoes ?? 'N/A' }}</td>
                                                    <td>
                                                        <a href="{{ route('mortes.show', $morte->id) }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('mortes.edit', $morte->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('mortes.destroy', $morte->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este registro de morte? Se for uma ave individual, ela será reativada. Se for de um plantel, a quantidade será revertida.');">
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
                                                    <td colspan="8" class="text-center">Nenhum registro de morte encontrado.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                {{ $mortes->links('pagination::bootstrap-4') }}
                            </div>
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
