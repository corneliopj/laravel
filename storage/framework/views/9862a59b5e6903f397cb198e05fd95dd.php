<?php
    $pageTitle = 'Criar Reserva';
?>

<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Inclui Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Inclui AdminLTE Select2 customizações (se houver) -->
<link rel="stylesheet" href="<?php echo e(asset('dist/css/adminlte.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')); ?>">


<div class="wrapper">
    <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Criar Nova Reserva</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('financeiro.reservas.index')); ?>">Reservas</a></li>
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
                            <form action="<?php echo e(route('financeiro.reservas.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="data_reserva">Data da Reserva <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control <?php $__errorArgs = ['data_reserva'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_reserva" name="data_reserva" value="<?php echo e(old('data_reserva', date('Y-m-d'))); ?>" required>
                                            <?php $__errorArgs = ['data_reserva'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="data_prevista_entrega">Data Prevista de Entrega</label>
                                            <input type="date" class="form-control <?php $__errorArgs = ['data_prevista_entrega'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_prevista_entrega" name="data_prevista_entrega" value="<?php echo e(old('data_prevista_entrega')); ?>">
                                            <?php $__errorArgs = ['data_prevista_entrega'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="data_vencimento_proposta">Data Vencimento Proposta</label>
                                            <input type="date" class="form-control <?php $__errorArgs = ['data_vencimento_proposta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_vencimento_proposta" name="data_vencimento_proposta" value="<?php echo e(old('data_vencimento_proposta')); ?>">
                                            <?php $__errorArgs = ['data_vencimento_proposta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="nome_cliente">Nome do Cliente</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['nome_cliente'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="nome_cliente" name="nome_cliente" value="<?php echo e(old('nome_cliente')); ?>" placeholder="Nome completo do cliente">
                                            <?php $__errorArgs = ['nome_cliente'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="contato_cliente">Contato do Cliente</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['contato_cliente'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="contato_cliente" name="contato_cliente" value="<?php echo e(old('contato_cliente')); ?>" placeholder="Telefone ou E-mail">
                                            <?php $__errorArgs = ['contato_cliente'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea class="form-control <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="observacoes" name="observacoes" rows="3" placeholder="Observações sobre a reserva"><?php echo e(old('observacoes')); ?></textarea>
                                        <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="pagamento_parcial">Pagamento Parcial (Sinal)</label>
                                        <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['pagamento_parcial'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="pagamento_parcial" name="pagamento_parcial" value="<?php echo e(old('pagamento_parcial', 0.00)); ?>" min="0">
                                        <?php $__errorArgs = ['pagamento_parcial'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <hr>
                                    <h4>Itens da Reserva</h4>
                                    <div id="items-container">
                                        <?php if(old('items')): ?>
                                            <?php $__currentLoopData = old('items'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php echo $__env->make('financeiro.reservas.partials.item_form_fields', ['index' => $index, 'item' => $item, 'avesDisponiveis' => $avesDisponiveis], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <?php echo $__env->make('financeiro.reservas.partials.item_form_fields', ['index' => 0, 'item' => null, 'avesDisponiveis' => $avesDisponiveis], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm mt-3" id="add-item">Adicionar Item</button>
                                    <?php $__errorArgs = ['items'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger mt-2">
                                            <strong><?php echo e($message); ?></strong>
                                        </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <?php $__errorArgs = ['items.*.descricao_item'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger mt-2">
                                            <strong><?php echo e($message); ?></strong>
                                        </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <?php $__errorArgs = ['items.*.quantidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger mt-2">
                                            <strong><?php echo e($message); ?></strong>
                                        </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <?php $__errorArgs = ['items.*.preco_unitario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger mt-2">
                                            <strong><?php echo e($message); ?></strong>
                                        </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Reserva</button>
                                    <a href="<?php echo e(route('financeiro.reservas.index')); ?>" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php echo $__env->make('layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
                    url: '<?php echo e(route('financeiro.reservas.searchAvesForReserva')); ?>',
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
            // Se houver um valor pré-selecionado (old('items.*.ave_id')), busca e define o texto
            const aveId = $(this).val();
            if (aveId) {
                const $element = $(this);
                $.ajax({
                    url: '<?php echo e(route('financeiro.reservas.searchAvesForReserva')); ?>',
                    dataType: 'json',
                    data: { q: '' }, // Query vazia para buscar por ID
                    success: function(data) {
                        const ave = data.find(a => a.id == aveId);
                        if (ave) {
                            const option = new Option(ave.text, ave.id, true, true);
                            $element.append(option).trigger('change');
                            const $row = $element.closest('.item-row');
                            const $descricaoField = $row.find('input[name$="[descricao_item]"]');
                            $descricaoField.val(ave.text);
                        }
                    }
                });
            }
        });


        // Adicionar novo item
        $('#add-item').on('click', function() {
            const newItemHtml = `
                <?php echo $__env->make('financeiro.reservas.partials.item_form_fields', ['index' => '__INDEX__', 'item' => null, 'avesDisponiveis' => $avesDisponiveis], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/reservas/create.blade.php ENDPATH**/ ?>