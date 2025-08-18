@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    $pageTitle = 'Contracheque';
@endphp

{{-- Inclui o partial head (APENAS CSS) --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')
    {{-- Inclui o partial sidebar --}}
    @include('layouts.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Contracheque</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Contracheque</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Filtros de Mês e Ano -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Filtrar por Período</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('financeiro.contracheque.index') }}" method="GET" class="form-inline">
                                    <div class="form-group mr-3">
                                        <label for="mes" class="mr-2">Mês:</label>
                                        <select name="mes" id="mes" class="form-control form-control-sm">
                                            @foreach ($mesesDoAno as $mesItem)
                                                <option value="{{ $mesItem['numero'] }}" {{ $mes == $mesItem['numero'] ? 'selected' : '' }}>{{ ucfirst($mesItem['nome']) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="ano" class="mr-2">Ano:</label>
                                        <select name="ano" id="ano" class="form-control form-control-sm">
                                            @foreach ($anosDisponiveis as $anoItem)
                                                <option value="{{ $anoItem }}" {{ $ano == $anoItem ? 'selected' : '' }}>{{ $anoItem }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Aplicar Filtro</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
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
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Contracheque do Mês -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    Contracheque do Mês ({{ ucfirst($nomeMes) }}/{{ $ano }})
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            <tr>
                                                <td>+ Salário</td>
                                                <td class="text-right">R$ {{ number_format($contrachequeSumario['salario'], 2, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>+ Comissões</td>
                                                <td class="text-right">R$ {{ number_format($contrachequeSumario['comissoes'], 2, ',', '.') }}</td>
                                            </tr>
                                            @if ($contrachequeSumario['outros_positivos'] > 0)
                                            <tr>
                                                <td>+ Outros Proventos</td>
                                                <td class="text-right">R$ {{ number_format($contrachequeSumario['outros_positivos'], 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td>- Adiantamento</td>
                                                <td class="text-right">R$ {{ number_format($contrachequeSumario['adiantamento'], 2, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>- Cartão de crédito</td>
                                                <td class="text-right">R$ {{ number_format($contrachequeSumario['cartao_credito'], 2, ',', '.') }}</td>
                                            </tr>
                                            @if ($contrachequeSumario['outros_negativos'] > 0)
                                            <tr>
                                                <td>- Outros Descontos</td>
                                                <td class="text-right">R$ {{ number_format($contrachequeSumario['outros_negativos'], 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            <tr class="table-secondary">
                                                <td><strong>Valor Bruto:</strong></td>
                                                <td class="text-right"><strong>R$ {{ number_format($contrachequeSumario['valor_bruto'], 2, ',', '.') }}</strong></td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <td><strong>Descontos:</strong></td>
                                                <td class="text-right"><strong>R$ {{ number_format($contrachequeSumario['descontos'], 2, ',', '.') }}</strong></td>
                                            </tr>
                                            <tr class="table-primary">
                                                <td><strong>Saldo Líquido:</strong></td>
                                                <td class="text-right"><strong>R$ {{ number_format($contrachequeSumario['saldo_liquido'], 2, ',', '.') }}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <h4>Lançamentos Detalhados:</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Descrição</th>
                                                <th>Tipo</th>
                                                <th class="text-right">Valor</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($contrachequeSumario['lancamentos_detalhados'] as $lancamento)
                                                <tr>
                                                    <td>{{ $lancamento['data'] }}</td>
                                                    <td>
                                                        @if (isset($lancamento['venda_id']))
                                                            <a href="javascript:void(0);" 
                                                               tabindex="0"
                                                               class="venda-popover" 
                                                               role="button"
                                                               data-toggle="popover" 
                                                               title="<i class='fas fa-shopping-cart'></i> Detalhes da Venda #{{ $lancamento['venda_id'] }}"
                                                               data-content-id="popover-content-{{ $lancamento['venda_id'] }}">
                                                                {{ $lancamento['descricao'] }} <i class="fas fa-info-circle text-info ml-1"></i>
                                                            </a>
                                                            
                                                            <!-- Hidden content for popover -->
                                                            <div id="popover-content-{{ $lancamento['venda_id'] }}" class="d-none">
                                                                <strong>Valor Final:</strong> R$ {{ number_format($lancamento['venda_detalhes']['valor_final'], 2, ',', '.') }}<br>
                                                                <strong>Observações:</strong> {{ $lancamento['venda_detalhes']['observacoes'] ?: 'Nenhuma' }}<br>
                                                                <strong>Itens:</strong>
                                                                <ul class='list-unstyled mt-1 mb-0'>
                                                                    @foreach($lancamento['venda_detalhes']['itens'] as $item)
                                                                        <li>- {{ $item['quantidade'] }}x {{ $item['descricao'] }} (R$ {{ number_format($item['valor_total_item'], 2, ',', '.') }})</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @else
                                                            {{ $lancamento['descricao'] }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($lancamento['tipo_lancamento'] == 'positivo')
                                                            <span class="badge badge-success">Positivo</span>
                                                        @else
                                                            <span class="badge badge-danger">Negativo</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">R$ {{ number_format($lancamento['valor'], 2, ',', '.') }}</td>
                                                    <td>
                                                        @if (isset($lancamento['id']))
                                                            <form action="{{ route('financeiro.contracheque.destroy', $lancamento['id']) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este lançamento?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-xs" title="Excluir Lançamento"><i class="fas fa-trash"></i></button>
                                                            </form>
                                                        @else
                                                            <button class="btn btn-secondary btn-xs" disabled title="Comissões são gerenciadas na respectiva venda e não podem ser excluídas aqui."><i class="fas fa-trash"></i></button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Nenhum lançamento de contracheque encontrado para este mês.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <hr>

                                <h4>Adicionar Novo Lançamento:</h4>
                                <form action="{{ route('financeiro.contracheque.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                                    <div class="form-group">
                                        <label for="descricao_contracheque">Descrição</label>
                                        <input type="text" name="descricao" id="descricao_contracheque" class="form-control form-control-sm" required value="{{ old('descricao') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="valor_contracheque">Valor</label>
                                        <input type="number" step="0.01" name="valor" id="valor_contracheque" class="form-control form-control-sm" required min="0.01" value="{{ old('valor') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_lancamento_contracheque">Tipo de Lançamento</label>
                                        <select name="tipo_lancamento" id="tipo_lancamento_contracheque" class="form-control form-control-sm" required>
                                            <option value="positivo" {{ old('tipo_lancamento') == 'positivo' ? 'selected' : '' }}>Positivo</option>
                                            <option value="negativo" {{ old('tipo_lancamento') == 'negativo' ? 'selected' : '' }}>Negativo</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="data_contracheque">Data</label>
                                        <input type="date" name="data" id="data_contracheque" class="form-control form-control-sm" value="{{ old('data', Carbon::now()->format('Y-m-d')) }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Adicionar Lançamento</button>
                                </form>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
<!-- ./wrapper -->

{{-- Inclui o partial scripts (TODOS OS SCRIPTS GLOBAIS NA ORDEM CORRETA) --}}
@include('layouts.partials.scripts')

{{-- QUAISQUER SCRIPTS ESPECÍFICOS DESTA VIEW DEVEM VIR AQUI, DEPOIS DE scripts.blade.php --}}
{{-- E SEMPRE DENTRO DE $(document).ready() --}}
<script>
    $(document).ready(function() {
        // Inicializa os popovers para os detalhes da venda.
        // Usar o trigger 'focus' permite que o popover seja aberto com um clique
        // e fechado automaticamente ao clicar em qualquer outro lugar da página.
        $('.venda-popover').popover({
            html: true,
            trigger: 'focus',
            placement: 'top',
            container: 'body', // Para evitar problemas de layout dentro de tabelas
            content: function () {
                var content_id = $(this).data('content-id');
                return $('#' + content_id).html();
            }
        });
    });
</script>
