@php
    $pageTitle = 'Detalhes da Transação Recorrente';
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
                        <h1 class="m-0">Detalhes da Transação Recorrente</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.transacoes_recorrentes.index') }}">Transações Recorrentes</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">{{ $transacaoRecorrente->description }}</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>ID:</strong> {{ $transacaoRecorrente->id }}</p>
                                <p><strong>Descrição:</strong> {{ $transacaoRecorrente->description }}</p>
                                <p><strong>Valor:</strong> R$ {{ number_format($transacaoRecorrente->value, 2, ',', '.') }}</p>
                                <p><strong>Categoria:</strong> {{ $transacaoRecorrente->categoria->nome ?? 'N/A' }} ({{ ucfirst($transacaoRecorrente->categoria->tipo ?? '') }})</p>
                                <p><strong>Tipo:</strong>
                                    <span class="badge badge-{{ $transacaoRecorrente->type == 'receita' ? 'success' : 'danger' }}">
                                        {{ ucfirst($transacaoRecorrente->type) }}
                                    </span>
                                </p>
                                <p><strong>Frequência:</strong>
                                    @php
                                        $frequencias = [
                                            'daily' => 'Diária',
                                            'weekly' => 'Semanal',
                                            'monthly' => 'Mensal',
                                            'quarterly' => 'Trimestral',
                                            'yearly' => 'Anual'
                                        ];
                                    @endphp
                                    {{ $frequencias[$transacaoRecorrente->frequency] ?? $transacaoRecorrente->frequency }}
                                </p>
                                <p><strong>Data de Início:</strong> {{ $transacaoRecorrente->start_date->format('d/m/Y') }}</p>
                                <p><strong>Data de Fim:</strong> {{ $transacaoRecorrente->end_date ? $transacaoRecorrente->end_date->format('d/m/Y') : 'N/A' }}</p>
                                <p><strong>Próximo Vencimento:</strong> {{ $transacaoRecorrente->next_due_date ? $transacaoRecorrente->next_due_date->format('d/m/Y') : 'N/A' }}</p>
                                <p><strong>Última Geração:</strong> {{ $transacaoRecorrente->last_generated_date ? $transacaoRecorrente->last_generated_date->format('d/m/Y') : 'Nunca' }}</p>
                                <p><strong>Criado em:</strong> {{ $transacaoRecorrente->created_at->format('d/m/Y H:i:s') }}</p>
                                <p><strong>Última Atualização:</strong> {{ $transacaoRecorrente->updated_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('financeiro.transacoes_recorrentes.edit', $transacaoRecorrente->id) }}" class="btn btn-warning">Editar</a>
                                <a href="{{ route('financeiro.transacoes_recorrentes.index') }}" class="btn btn-secondary">Voltar para a Lista</a>
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
