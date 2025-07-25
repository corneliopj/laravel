@php
    $pageTitle = 'Registrar Nova Receita';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        {{-- Inclui o partial navbar --}}
        @include('layouts.partials.navbar')
        {{-- Inclui o partial sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- CONTEÚDO PRINCIPAL DA PÁGINA --}}
        <div class="content-wrapper px-4 py-2" style="min-height:797px;">
            {{-- Cabeçalho do Conteúdo --}}
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">{{ $pageTitle }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('financeiro.receitas.index') }}">Receitas Financeiras</a></li>
                                <li class="breadcrumb-item active">Registrar</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conteúdo Principal --}}
            <div class="content">
                <div class="container-fluid">
                    {{-- Mensagens de sucesso/erro --}}
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

                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Nova Receita</h3>
                        </div>
                        <form action="{{ route('financeiro.receitas.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="descricao">Descrição:</label>
                                    <input type="text" name="descricao" id="descricao" class="form-control @error('descricao') is-invalid @enderror" value="{{ old('descricao') }}" placeholder="Ex: Venda de aves, Salário, Reembolso" required>
                                    @error('descricao')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="categoria_id">Categoria:</label>
                                    <select name="categoria_id" id="categoria_id" class="form-control select2 @error('categoria_id') is-invalid @enderror" style="width: 100%;" required>
                                        <option value="">Selecione a Categoria</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="valor">Valor:</label>
                                    <input type="number" name="valor" id="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor') }}" step="0.01" min="0" required>
                                    @error('valor')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="data">Data:</label>
                                    <input type="date" name="data" id="data" class="form-control @error('data') is-invalid @enderror" value="{{ old('data', date('Y-m-d')) }}" required>
                                    @error('data')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="observacoes">Observações:</label>
                                    <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3" placeholder="Detalhes adicionais sobre a receita">{{ old('observacoes') }}</textarea>
                                    @error('observacoes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">Salvar Receita</button>
                                <a href="{{ route('financeiro.receitas.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- FIM DO CONTEÚDO PRINCIPAL DA PÁGINA --}}

        {{-- Inclui o partial footer --}}
        @include('layouts.partials.footer')
    </div>
    {{-- Fim do div.wrapper --}}

    <script>
        $(function () {
            // Inicializar Select2 para o campo de categoria
            $('#categoria_id').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
</body>
</html>
