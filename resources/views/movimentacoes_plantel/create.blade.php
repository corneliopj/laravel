@php
    $pageTitle = 'Registrar Nova Movimentação de Plantel';
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
                        <h1>Registrar Nova Movimentação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('plantel.index') }}">Plantéis</a></li>
                            @if ($plantelPreSelecionado)
                                <li class="breadcrumb-item"><a href="{{ route('plantel.show', $plantelPreSelecionado->id) }}">Detalhes do Plantel</a></li>
                            @endif
                            <li class="breadcrumb-item active">Nova Movimentação</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados da Movimentação</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action="{{ route('movimentacoes-plantel.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="plantel_id">Plantel</label>
                                        <select name="plantel_id" id="plantel_id" class="form-control @error('plantel_id') is-invalid @enderror" required {{ $plantelPreSelecionado ? 'disabled' : '' }}>
                                            <option value="">Selecione um Plantel</option>
                                            @foreach($plantelOptions as $plantel)
                                                <option value="{{ $plantel->id }}"
                                                    {{ old('plantel_id', $plantelPreSelecionado->id ?? '') == $plantel->id ? 'selected' : '' }}>
                                                    {{ $plantel->identificacao_grupo }} (Qtd. Atual: {{ $plantel->quantidade_atual }})
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- Campo hidden para enviar o ID se o select estiver desabilitado --}}
                                        @if ($plantelPreSelecionado)
                                            <input type="hidden" name="plantel_id" value="{{ $plantelPreSelecionado->id }}">
                                        @endif
                                        @error('plantel_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_movimentacao">Tipo de Movimentação</label>
                                        <select name="tipo_movimentacao" id="tipo_movimentacao" class="form-control @error('tipo_movimentacao') is-invalid @enderror" required>
                                            <option value="">Selecione o Tipo</option>
                                            <option value="entrada" {{ old('tipo_movimentacao') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                            <option value="saida_venda" {{ old('tipo_movimentacao') == 'saida_venda' ? 'selected' : '' }}>Saída (Venda)</option>
                                            <option value="saida_morte" {{ old('tipo_movimentacao') == 'saida_morte' ? 'selected' : '' }}>Saída (Morte)</option>
                                            <option value="saida_consumo" {{ old('tipo_movimentacao') == 'saida_consumo' ? 'selected' : '' }}>Saída (Consumo)</option>
                                            <option value="saida_doacao" {{ old('tipo_movimentacao') == 'saida_doacao' ? 'selected' : '' }}>Saída (Doação)</option>
                                            <option value="saida_descarte" {{ old('tipo_movimentacao') == 'saida_descarte' ? 'selected' : '' }}>Saída (Descarte)</option>
                                            <option value="outros" {{ old('tipo_movimentacao') == 'outros' ? 'selected' : '' }}>Outros</option>
                                        </select>
                                        @error('tipo_movimentacao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="quantidade">Quantidade</label>
                                        <input type="number" name="quantidade" id="quantidade" class="form-control @error('quantidade') is-invalid @enderror" value="{{ old('quantidade') }}" min="1" placeholder="Quantidade de aves" required>
                                        @error('quantidade')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="data_movimentacao">Data da Movimentação</label>
                                        <input type="date" name="data_movimentacao" id="data_movimentacao" class="form-control @error('data_movimentacao') is-invalid @enderror" value="{{ old('data_movimentacao', date('Y-m-d')) }}" required>
                                        @error('data_movimentacao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3" placeholder="Notas sobre esta movimentação...">{{ old('observacoes') }}</textarea>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Registrar Movimentação</button>
                                    <a href="{{ $plantelPreSelecionado ? route('plantel.show', $plantelPreSelecionado->id) : route('movimentacoes-plantel.index') }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
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
