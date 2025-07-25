@php
    $pageTitle = 'Listagem de Acasalamentos';
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
                        <h1 class="m-0">Listagem de Acasalamentos</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Acasalamentos</li>
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
                            {{-- Botão Novo Acasalamento --}}
                            <div class="mb-3">
                                <a href="{{ route('acasalamentos.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Novo Acasalamento
                                </a>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Macho (Matrícula)</th>
                                        <th>Fêmea (Matrícula)</th>
                                        <th>Data Início</th>
                                        <th>Data Fim</th>
                                        <th>Observações</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($acasalamentos as $acasalamento)
                                        <tr>
                                            <td>{{ $acasalamento->id }}</td>
                                            <td>{{ $acasalamento->macho->matricula ?? 'N/A' }}</td>
                                            <td>{{ $acasalamento->femea->matricula ?? 'N/A' }}</td>
                                            <td>{{ $acasalamento->data_inicio->format('d/m/Y') }}</td>
                                            <td>{{ $acasalamento->data_fim ? $acasalamento->data_fim->format('d/m/Y') : 'Em andamento' }}</td>
                                            <td>{{ $acasalamento->observacoes ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('acasalamentos.edit', $acasalamento->id) }}" class="btn btn-sm btn-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- NOVO: Botão para "Encerrar Acasalamento" --}}
                                                @if (!$acasalamento->data_fim) {{-- Mostra o botão apenas se o acasalamento estiver em andamento --}}
                                                    <a href="{{ route('acasalamentos.edit', $acasalamento->id) }}" class="btn btn-sm btn-warning" title="Encerrar Acasalamento" onclick="return confirm('Tem certeza que deseja encerrar este acasalamento? Isso registrará a data de fim.');">
                                                        <i class="fas fa-times-circle"></i> Encerrar
                                                    </a>
                                                @else
                                                    {{-- Opcional: Adicionar um botão de exclusão permanente (com mais cautela) --}}
                                                    <form action="{{ route('acasalamentos.destroy', $acasalamento->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir Permanentemente" onclick="return confirm('ATENÇÃO: Tem certeza que deseja EXCLUIR PERMANENTEMENTE este acasalamento? Esta ação é irreversível.');">
                                                            <i class="fas fa-trash"></i> Excluir
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7">Nenhum acasalamento registado.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
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
