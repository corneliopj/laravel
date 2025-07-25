@php
    $pageTitle = 'Lista de Aves';
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
                        <h1 class="m-0">Lista de Aves</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Aves</li>
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
                                <h3 class="card-title">Gerenciamento de Aves</h3>
                                <div class="card-tools">
                                    <a href="{{ route('aves.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Ave
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                {{-- Mensagens de sucesso ou erro --}}
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

                                {{-- Filtros de Status --}}
                                <div class="mb-3">
                                    <strong class="mr-2">Filtrar por Status:</strong>
                                    <a href="{{ route('aves.index', ['status' => 'ativas']) }}"
                                       class="btn btn-outline-primary btn-sm {{ request('status') == 'ativas' || (!request('status') && !request()->has('status')) ? 'active' : '' }}">
                                        Ativas
                                    </a>
                                    <a href="{{ route('aves.index', ['status' => 'excluidas']) }}"
                                       class="btn btn-outline-warning btn-sm {{ request('status') == 'excluidas' ? 'active' : '' }}">
                                        Excluídas
                                    </a>
                                    <a href="{{ route('aves.index', ['status' => 'mortas']) }}"
                                       class="btn btn-outline-danger btn-sm {{ request('status') == 'mortas' ? 'active' : '' }}">
                                        Mortas
                                    </a>
                                    <a href="{{ route('aves.index', ['status' => 'inativas']) }}"
                                       class="btn btn-outline-secondary btn-sm {{ request('status') == 'inativas' ? 'active' : '' }}">
                                        Todas Inativas
                                    </a>
                                    <a href="{{ route('aves.index') }}"
                                       class="btn btn-outline-info btn-sm {{ !request('status') && request()->has('status') ? 'active' : '' }}">
                                        Todas as Aves
                                    </a>
                                </div>

                                {{-- Tabela de Aves --}}
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Imagem</th> {{-- Coluna para a imagem --}}
                                            <th>Matrícula</th>
                                            <th>Tipo de Ave</th>
                                            <th>Variação</th>
                                            <th>Lote</th>
                                            <th>Data Eclosão</th>
                                            <th>Sexo</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($aves as $ave)
                                            <tr>
                                                <td>{{ $ave->id }}</td>
                                                <td>
                                                    {{-- Exibe a foto da ave ou a silhueta correspondente --}}
                                                    @if ($ave->foto_path)
                                                        <img src="{{ asset($ave->foto_path) }}" alt="Foto da Ave" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        @php
                                                            $birdTypeName = strtolower($ave->tipoAve->nome ?? '');
                                                            $silhouetteFileName = '';
                                                            $silhouetteMap = [
                                                                'galinaceos' => 'galinha.png',
                                                                'perus'      => 'peru.png',
                                                                'marrecos'   => 'mareco.png',
                                                                'angolas'    => 'angola.png',
                                                                'codornas'   => 'codorna.png',
                                                                'gansos'     => 'ganso.png', // Certifique-se de ter 'ganso.png' em public/img/
                                                            ];
                                                            $silhouetteFileName = $silhouetteMap[$birdTypeName] ?? null;
                                                            $silhouetteSrc = $silhouetteFileName ? asset('img/' . $silhouetteFileName) : null;
                                                            $placeholderText = ucfirst($birdTypeName) ?: 'Ave';
                                                            // O placeholder genérico será exibido apenas se a silhueta específica não for encontrada.
                                                            // Usa a primeira letra do tipo de ave para o texto do placeholder.
                                                            $genericPlaceholderSrc = 'https://placehold.co/50x50/e0e0e0/000000?text=' . urlencode(substr($placeholderText, 0, 1));
                                                        @endphp
                                                        <img src="{{ $silhouetteSrc }}"
                                                             alt="Silhueta da Ave"
                                                             class="img-thumbnail"
                                                             style="width: 50px; height: 50px; object-fit: cover;"
                                                             onerror="this.onerror=null; this.src='{{ $genericPlaceholderSrc }}';" {{-- Fallback para placeholder se silhueta não carregar --}}
                                                        >
                                                    @endif
                                                </td>
                                                <td>{{ $ave->matricula }}</td>
                                                <td>{{ $ave->tipoAve->nome ?? 'N/A' }}</td>
                                                <td>{{ $ave->variacao->nome ?? 'N/A' }}</td>
                                                <td>{{ $ave->lote->identificacao_lote ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($ave->data_eclosao)->format('d/m/Y') }}</td>
                                                <td>{{ $ave->sexo }}</td>
                                                <td>
                                                    @if ($ave->ativo)
                                                        <span class="badge badge-success">Ativa</span>
                                                    @elseif ($ave->mortes()->exists())
                                                        <span class="badge badge-danger">Morta</span>
                                                    @elseif ($ave->trashed())
                                                        <span class="badge badge-warning">Inativa (Excluída)</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inativa</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('aves.show', $ave->id) }}" class="btn btn-info btn-sm mb-1" title="Ver Detalhes">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                    {{-- Botão Certidão: Visível para todas as aves --}}
                                                    <form action="{{ route('aves.expedirCertidao', $ave->id) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-sm mb-1" title="Expedir Certidão">
                                                            <i class="fas fa-file-alt"></i> Certidão
                                                        </button>
                                                    </form>
                                                    @if ($ave->ativo)
                                                        <a href="{{ route('aves.edit', $ave->id) }}" class="btn btn-warning btn-sm mb-1" title="Editar">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                        <a href="{{ route('aves.registerDeath', $ave->id) }}" class="btn btn-danger btn-sm mb-1" title="Registrar Morte">
                                                            <i class="fas fa-skull"></i> Morte
                                                        </a>
                                                        <form action="{{ route('aves.destroy', $ave->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-secondary btn-sm mb-1" onclick="return confirm('Tem certeza que deseja INATIVAR (EXCLUIR) esta ave? Ela será marcada como inativa e removida das listagens padrão, mas poderá ser restaurada.');" title="Inativar/Excluir">
                                                                <i class="fas fa-trash"></i> Inativar
                                                            </button>
                                                        </form>
                                                    @elseif ($ave->trashed() && !$ave->mortes()->exists()) {{-- Se foi soft-deletada e NÃO tem registro de morte --}}
                                                        <form action="{{ route('aves.restore', $ave->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm mb-1" onclick="return confirm('Tem certeza que deseja RESTAURAR esta ave?');" title="Restaurar">
                                                                <i class="fas fa-undo"></i> Restaurar
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('aves.forceDelete', $ave->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Tem certeza que deseja EXCLUIR PERMANENTEMENTE esta ave? Esta ação é irreversível.');" title="Excluir Permanentemente">
                                                                <i class="fas fa-bomb"></i> Excluir Def.
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Nenhuma ave encontrada.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- Paginação --}}
                                <div class="d-flex justify-content-center">
                                    {{ $aves->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
