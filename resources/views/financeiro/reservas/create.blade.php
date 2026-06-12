@php
    $pageTitle = 'Criar Reserva';
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Criar Nova Reserva</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.reservas.index') }}">Reservas</a></li>
                            <li class="breadcrumb-item active">Criar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados da Reserva</h3>
                            </div>
                            <form action="{{ route('financeiro.reservas.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="data_reserva">Data da Reserva <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('data_reserva') is-invalid @enderror" id="data_reserva" name="data_reserva" value="{{ old('data_reserva', date('Y-m-d')) }}" required>
                                            @error('data_reserva')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="data_prevista_entrega">Data Prevista de Entrega</label>
                                            <input type="date" class="form-control @error('data_prevista_entrega') is-invalid @enderror" id="data_prevista_entrega" name="data_prevista_entrega" value="{{ old('data_prevista_entrega') }}">
                                            @error('data_prevista_entrega')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="data_vencimento_proposta">Data Vencimento Proposta</label>
                                            <input type="date" class="form-control @error('data_vencimento_proposta') is-invalid @enderror" id="data_vencimento_proposta" name="data_vencimento_proposta" value="{{ old('data_vencimento_proposta') }}">
                                            @error('data_vencimento_proposta')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="nome_cliente">Nome do Cliente</label>
                                            <input type="text" class="form-control @error('nome_cliente') is-invalid @enderror" id="nome_cliente" name="nome_cliente" value="{{ old('nome_cliente') }}" placeholder="Nome completo do cliente">
                                            @error('nome_cliente')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="contato_cliente">Contato do Cliente</label>
                                            <input type="text" class="form-control @error('contato_cliente') is-invalid @enderror" id="contato_cliente" name="contato_cliente" value="{{ old('contato_cliente') }}" placeholder="Telefone ou E-mail">
                                            @error('contato_cliente')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3" placeholder="Observações sobre a reserva">{{ old('observacoes') }}</textarea>
                                        @error('observacoes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="pagamento_parcial">Pagamento Parcial (Sinal)</label>
                                        <input type="number" step="0.01" class="form-control @error('pagamento_parcial') is-invalid @enderror" id="pagamento_parcial" name="pagamento_parcial" value="{{ old('pagamento_parcial', 0.00) }}" min="0">
                                        @error('pagamento_parcial')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <hr>
                                    <h4>Itens da Reserva</h4>
                                    <div id="items-container">
                                        @if (old('items'))
                                            @foreach (old('items') as $index => $item)
                                                @include('financeiro.reservas.partials.item_form_fields', ['index' => $index, 'item' => $item, 'avesDisponiveis' => $avesDisponiveis])
                                            @endforeach
                                        @else
                                            @include('financeiro.reservas.partials.item_form_fields', ['index' => 0, 'item' => null, 'avesDisponiveis' => $avesDisponiveis])
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm mt-3" id="add-item">Adicionar Item</button>
                                    @error('items')
                                        <div class="text-danger mt-2">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                    @error('items.*.descricao_item')
                                        <div class="text-danger mt-2">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                    @error('items.*.quantidade')
                                        <div class="text-danger mt-2">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                    @error('items.*.preco_unitario')
                                        <div class="text-danger mt-2">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Reserva</button>
                                    <a href="{{ route('financeiro.reservas.index') }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
