@php
    $pageTitle = 'Editar Reserva';
@endphp

@include('layouts.partials.head')

<!-- Inclui Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Inclui AdminLTE Select2 customizações (se houver) -->
<link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Editar Reserva #{{ $reserva->numero_reserva }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.reservas.index') }}">Reservas</a></li>
                            <li class="breadcrumb-item active">Editar</li>
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
                            <form action="{{ route('financeiro.reservas.update', $reserva->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="data_reserva">Data da Reserva <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('data_reserva') is-invalid @enderror" id="data_reserva" name="data_reserva" value="{{ old('data_reserva', $reserva->data_reserva->format('Y-m-d')) }}" required>
                                            @error('data_reserva')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="data_prevista_entrega">Data Prevista de Entrega</label>
                                            <input type="date" class="form-control @error('data_prevista_entrega') is-invalid @enderror" id="data_prevista_entrega" name="data_prevista_entrega" value="{{ old('data_prevista_entrega', $reserva->data_prevista_entrega ? $reserva->data_prevista_entrega->format('Y-m-d') : '') }}">
                                            @error('data_prevista_entrega')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="data_vencimento_proposta">Data Vencimento Proposta</label>
                                            <input type="date" class="form-control @error('data_vencimento_proposta') is-invalid @enderror" id="data_vencimento_proposta" name="data_vencimento_proposta" value="{{ old('data_vencimento_proposta', $reserva->data_vencimento_proposta ? $reserva->data_vencimento_proposta->format('Y-m-d') : '') }}">
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
                                            <input type="text" class="form-control @error('nome_cliente') is-invalid @enderror" id="nome_cliente" name="nome_cliente" value="{{ old('nome_cliente', $reserva->nome_cliente) }}" placeholder="Nome completo do cliente">
                                            @error('nome_cliente')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="contato_cliente">Contato do Cliente</label>
                                            <input type="text" class="form-control @error('contato_cliente') is-invalid @enderror" id="contato_cliente" name="contato_cliente" value="{{ old('contato_cliente', $reserva->contato_cliente) }}" placeholder="Telefone ou E-mail">
                                            @error('contato_cliente')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3" placeholder="Observações sobre a reserva">{{ old('observacoes', $reserva->observacoes) }}</textarea>
                                        @error('observacoes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="pagamento_parcial">Pagamento Parcial (Sinal)</label>
                                        <input type="number" step="0.01" class="form-control @error('pagamento_parcial') is-invalid @enderror" id="pagamento_parcial" name="pagamento_parcial" value="{{ old('pagamento_parcial', $reserva->pagamento_parcial) }}" min="0">
                                        @error('pagamento_parcial')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status da Reserva <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                            @foreach ($statusOptions as $key => $value)
                                                <option value="{{ $key }}" {{ old('status', $reserva->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <hr>
                                    <h4>Itens da Reserva</h4>
                                    <div id="items-container">
                                        @foreach ($reserva->items as $index => $item)
                                            @include('financeiro.reservas.partials.item_form_fields', ['index' => $index, 'item' => $item, 'avesDisponiveis' => $avesDisponiveis])
                                        @endforeach
                                        @if (old('items'))
                                            @foreach (old('items') as $index => $item)
                                                @if (!isset($reserva->items[$index])) {{-- Adiciona itens que foram adicionados via old() mas não estavam na reserva original --}}
                                                    @include('financeiro.reservas.partials.item_form_fields', ['index' => $index, 'item' => $item, 'avesDisponiveis' => $avesDisponiveis])
                                                @endif
                                            @endforeach
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
                                    <button type="submit" class="btn btn-primary">Atualizar Reserva</button>
                                    <a href="{{ route('financeiro.reservas.index') }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('layouts.partials.footer')
</div>

@include('layouts.partials.scripts')

<!-- Inclui Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        let itemIndex = $('#items-container .item-row').length > 0 ? $('#items-container .item-row').last().data('index') + 1 : 0;

        // Função para inicializar Select2
        function initializeSelect2(selector) {
            $(selector).select2({
                placeholder: 'Selecione uma ave ou digite a descrição',
                allowClear: true,
                theme: 'bootstrap4',
                ajax: {
                    url: '{{ route('financeiro.reservas.searchAvesForReserva') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    preco_sugerido: item.preco_sugerido // Adiciona o preço sugerido
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                templateResult: function(data) {
                    if (data.loading) return data.text;
                    return data.text; // Exibe apenas a matrícula e tipo
                },
                templateSelection: function(data) {
                    if (data.id) {
                        return data.text; // Exibe a matrícula e tipo na seleção
                    }
                    return data.text;
                }
            }).on('select2:select', function (e) {
                const data = e.params.data;
                const $row = $(this).closest('.item-row');
                const $descricaoField = $row.find('input[name$="[descricao_item]"]');
                const $precoField = $row.find('input[name$="[preco_unitario]"]');

                // Preenche a descrição com a matrícula e tipo da ave
                $descricaoField.val(data.text);
                // Preenche o preço unitário com o preço sugerido da ave, se disponível
                if (data.preco_sugerido !== undefined) {
                    $precoField.val(parseFloat(data.preco_sugerido).toFixed(2));
                }
                calculateItemTotal($row); // Recalcula o total do item
            }).on('select2:unselect', function (e) {
                const $row = $(this).closest('.item-row');
                const $descricaoField = $row.find('input[name$="[descricao_item]"]');
                const $precoField = $row.find('input[name$="[preco_unitario]"]');

                // Limpa os campos quando a ave é desselecionada
                $descricaoField.val('');
                $precoField.val('0.00');
                calculateItemTotal($row); // Recalcula o total do item
            });
        }

        // Inicializa Select2 para itens existentes (se houver)
        $('.select2-ave').each(function() {
            initializeSelect2(this);
            // Se houver um valor pré-selecionado (ave_id), busca e define o texto
            const aveId = $(this).val();
            if (aveId) {
                const $element = $(this);
                // Assume que você tem a informação da ave no `avesDisponiveis` ou pode buscar via AJAX
                const aveData = @json($avesDisponiveis->keyBy('id'));
                const ave = aveData[aveId];
                if (ave) {
                    const option = new Option(`${ave.matricula} (${ave.tipo_ave.nome})`, ave.id, true, true);
                    $element.append(option).trigger('change');
                    const $row = $element.closest('.item-row');
                    const $descricaoField = $row.find('input[name$="[descricao_item]"]');
                    $descricaoField.val(`${ave.matricula} (${ave.tipo_ave.nome})`);
                } else {
                    // Fallback para buscar via AJAX se a ave não estiver em avesDisponiveis (ex: já foi vendida)
                    $.ajax({
                        url: '{{ route('financeiro.reservas.searchAvesForReserva') }}',
                        dataType: 'json',
                        data: { q: '' }, // Query vazia para buscar por ID
                        success: function(data) {
                            const foundAve = data.find(a => a.id == aveId);
                            if (foundAve) {
                                const option = new Option(foundAve.text, foundAve.id, true, true);
                                $element.append(option).trigger('change');
                                const $row = $element.closest('.item-row');
                                const $descricaoField = $row.find('input[name$="[descricao_item]"]');
                                $descricaoField.val(foundAve.text);
                            }
                        }
                    });
                }
            }
        });


        // Adicionar novo item
        $('#add-item').on('click', function() {
            const newItemHtml = `
                @include('financeiro.reservas.partials.item_form_fields', ['index' => '__INDEX__', 'item' => null, 'avesDisponiveis' => $avesDisponiveis])
            `.replace(/__INDEX__/g, itemIndex);
            $('#items-container').append(newItemHtml);
            initializeSelect2(`#item-ave-id-${itemIndex}`); // Inicializa Select2 para o novo item
            itemIndex++;
        });

        // Remover item
        $('#items-container').on('click', '.remove-item', function() {
            $(this).closest('.item-row').remove();
            calculateOverallTotal(); // Recalcula o total geral após remover
        });

        // Calcular total do item
        function calculateItemTotal($row) {
            const quantidade = parseFloat($row.find('input[name$="[quantidade]"]').val()) || 0;
            const precoUnitario = parseFloat($row.find('input[name$="[preco_unitario]"]').val()) || 0;
            const totalItem = quantidade * precoUnitario;
            $row.find('.item-total-display').text(totalItem.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
            $row.find('input[name$="[valor_total_item]"]').val(totalItem.toFixed(2)); // Atualiza campo oculto
            calculateOverallTotal();
        }

        // Calcular total geral
        function calculateOverallTotal() {
            let overallTotal = 0;
            $('#items-container .item-row').each(function() {
                const totalItem = parseFloat($(this).find('input[name$="[valor_total_item]"]').val()) || 0;
                overallTotal += totalItem;
            });
            // Opcional: exibir o total geral em algum lugar da página
            // $('#overall-total-display').text(overallTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        }

        // Event listeners para mudanças de quantidade e preço
        $('#items-container').on('input', 'input[name$="[quantidade]"], input[name$="[preco_unitario]"]', function() {
            calculateItemTotal($(this).closest('.item-row'));
        });

        // Inicializa o cálculo para itens existentes ao carregar a página
        $('#items-container .item-row').each(function() {
            calculateItemTotal($(this));
        });
    });
</script>
