@php
    $pageTitle = 'Ficha da Ave';
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
                        <h1 class="m-0">Ficha da Ave</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('aves.index') }}">Aves</a></li>
                            <li class="breadcrumb-item active">Ficha</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8"> {{-- Ajuste a largura conforme necessário --}}
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Informações da Ave: {{ $ave->matricula }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group text-center">
                                    {{-- Exibe a foto da ave ou a silhueta correspondente --}}
                                    @if ($ave->foto_path)
                                        <img src="{{ asset($ave->foto_path) }}"
                                             alt="Foto da Ave"
                                             class="img-fluid rounded"
                                             style="max-width: 200px; height: auto; border: 2px solid #ddd;">
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
                                                'gansos'     => 'ganso.png',
                                            ];
                                            $silhouetteFileName = $silhouetteMap[$birdTypeName] ?? null;
                                            $silhouetteSrc = $silhouetteFileName ? asset('img/' . $silhouetteFileName) : null;
                                            $placeholderText = ucfirst($birdTypeName) ?: 'Ave';
                                            $genericPlaceholderSrc = 'https://placehold.co/200x200/e0e0e0/000000?text=' . urlencode($placeholderText);
                                        @endphp
                                        <img src="{{ $silhouetteSrc }}"
                                             alt="Silhueta da Ave"
                                             class="img-fluid rounded"
                                             style="max-width: 200px; height: auto; border: 2px solid #ddd;"
                                             onerror="this.onerror=null; this.src='{{ $genericPlaceholderSrc }}';">
                                    @endif
                                    <hr>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>ID:</strong> {{ $ave->id }}</p>
                                        <p><strong>Matrícula:</strong> {{ $ave->matricula }}</p>
                                        <p><strong>Tipo de Ave:</strong> {{ $ave->tipoAve->nome ?? 'N/A' }}</p>
                                        <p><strong>Variação:</strong> {{ $ave->variacao->nome ?? 'N/A' }}</p>
                                        <p><strong>Sexo:</strong> {{ $ave->sexo }}</p>
                                        <p><strong>Data de Eclosão:</strong> {{ $ave->data_eclosao ? $ave->data_eclosao->format('d/m/Y') : 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Vendável:</strong> {{ $ave->vendavel ? 'Sim' : 'Não' }}</p>
                                        <p><strong>Lote:</strong> {{ $ave->lote->identificacao_lote ?? 'N/A' }}</p>
                                        <p><strong>Incubação ID:</strong> {{ $ave->incubacao->id ?? 'N/A' }}</p>
                                        <p><strong>Status:</strong>
                                            @if ($ave->morte)
                                                <span class="badge badge-danger">Morto</span>
                                            @elseif (!$ave->ativo)
                                                <span class="badge badge-warning">Inativo</span>
                                            @else
                                                <span class="badge badge-success">Ativo</span>
                                            @endif
                                        </p>
                                        @if ($ave->data_inativado)
                                            <p><strong>Data Inativação:</strong> {{ $ave->data_inativado->format('d/m/Y H:i:s') }}</p>
                                        @endif
                                        <p><strong>Código de Validação:</strong> {{ $ave->codigo_validacao_certidao ?? 'Não gerado' }}</p>
                                    </div>
                                </div>
                                <hr>

                                {{-- Detalhes da Morte (se houver) --}}
                                @if ($ave->morte)
                                    <h4>Detalhes da Morte</h4>
                                    <p><strong>Data da Morte:</strong> {{ $ave->morte->data_morte->format('d/m/Y') }}</p>
                                    <p><strong>Causa:</strong> {{ $ave->morte->causa ?? 'Não informada' }}</p>
                                    <p><strong>Observações:</strong> {{ $ave->morte->observacoes ?? 'N/A' }}</p>
                                    <hr>
                                @endif

                                {{-- Detalhes da Incubação (se houver) --}}
                                @if ($ave->incubacao)
                                    <h4>Detalhes da Incubação</h4>
                                    <p><strong>Data Entrada Incubadora:</strong> {{ $ave->incubacao->data_entrada_incubadora->format('d/m/Y') ?? 'N/A' }}</p>
                                    <p><strong>Data Prevista Eclosão:</strong> {{ $ave->incubacao->data_prevista_eclosao->format('d/m/Y') ?? 'N/A' }}</p>
                                    <p><strong>Quantidade Ovos:</strong> {{ $ave->incubacao->quantidade_ovos ?? 'N/A' }}</p>
                                    <p><strong>Quantidade Eclodidos:</strong> {{ $ave->incubacao->quantidade_eclodidos ?? 'N/A' }}</p>
                                    <p><strong>Incubação Ativa:</strong> {{ $ave->incubacao->ativo ? 'Sim' : 'Não' }}</p>
                                    <hr>
                                @endif

                                {{-- Detalhes da Filiação (se houver via acasalamento) --}}
                                @if ($ave->incubacao && $ave->incubacao->posturaOvo && $ave->incubacao->posturaOvo->acasalamento)
                                    @php
                                        $acasalamento = $ave->incubacao->posturaOvo->acasalamento;
                                        $macho = $acasalamento->macho;
                                        $femea = $acasalamento->femea;
                                    @endphp
                                    <h4>Filiação (Pais)</h4>
                                    <p>
                                        <strong>Pai:</strong> {{ $macho->matricula ?? 'N/A' }}
                                        @if ($macho) (<a href="{{ route('aves.show', $macho->id) }}">Ver Pai</a>) @endif
                                    </p>
                                    <p>
                                        <strong>Mãe:</strong> {{ $femea->matricula ?? 'N/A' }}
                                        @if ($femea) (<a href="{{ route('aves.show', $femea->id) }}">Ver Mãe</a>) @endif
                                    </p>
                                    <p><strong>Acasalamento ID:</strong> {{ $acasalamento->id }}</p>
                                    <hr>
                                @endif
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('aves.index') }}" class="btn btn-secondary">Voltar para a Lista</a>
                                <a href="{{ route('aves.edit', $ave->id) }}" class="btn btn-warning">Editar Ave</a>
                                {{-- Botão de Registrar Morte (adicionado à ficha) --}}
                                @if (!$ave->morte && $ave->ativo)
                                    <a href="{{ route('aves.registerDeath', $ave->id) }}" class="btn btn-dark">Registrar Morte</a>
                                @endif

                                {{-- Botão Emitir Certidão agora é um formulário POST --}}
                                <form action="{{ route('aves.expedirCertidao', $ave->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Emitir Certidão</button>
                                </form>
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
