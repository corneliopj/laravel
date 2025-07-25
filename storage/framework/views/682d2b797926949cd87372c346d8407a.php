<?php
    $pageTitle = 'Registrar Novo Acasalamento';
?>


<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="wrapper">
    
    <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Registrar Novo Acasalamento</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('acasalamentos.index')); ?>">Acasalamentos</a></li>
                            <li class="breadcrumb-item active">Registrar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8"> 
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados do Acasalamento</h3>
                            </div>
                            <form action="<?php echo e(route('acasalamentos.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?> 
                                <div class="card-body">
                                    
                                    <?php if($errors->any()): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul>
                                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    
                                    <?php if(session('success')): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?php echo e(session('success')); ?>

                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if(session('error')): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php echo e(session('error')); ?>

                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    
                                    <div class="form-group">
                                        <label>Selecione a Espécie do Acasalamento:</label><br>
                                        <div class="d-flex flex-wrap" id="tipo-ave-radios">
                                            <?php $__currentLoopData = $tiposAves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="form-check mr-3">
                                                    <input class="form-check-input" type="radio" name="selected_tipo_ave_id" id="tipo_ave_radio_<?php echo e($tipo->id); ?>" value="<?php echo e($tipo->id); ?>" 
                                                        <?php echo e((old('selected_tipo_ave_id') == $tipo->id) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="tipo_ave_radio_<?php echo e($tipo->id); ?>">
                                                        <?php echo e($tipo->nome); ?>

                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <?php $__errorArgs = ['selected_tipo_ave_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="macho_id">Macho (Ave)</label>
                                        <select name="macho_id" id="macho_id" class="form-control <?php $__errorArgs = ['macho_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="">-- Selecione o Macho --</option>
                                            <?php $__currentLoopData = $machos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $macho): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($macho->id); ?>" data-tipo-ave-id="<?php echo e($macho->tipo_ave_id); ?>" <?php echo e(old('macho_id') == $macho->id ? 'selected' : ''); ?>>
                                                    <?php echo e($macho->matricula); ?> (<?php echo e($macho->tipoAve->nome ?? 'N/A'); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['macho_id'];
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
                                        <label for="femea_id">Fêmea (Ave)</label>
                                        <select name="femea_id" id="femea_id" class="form-control <?php $__errorArgs = ['femea_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="">-- Selecione a Fêmea --</option>
                                            <?php $__currentLoopData = $femeas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $femea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($femea->id); ?>" data-tipo-ave-id="<?php echo e($femea->tipo_ave_id); ?>" <?php echo e(old('femea_id') == $femea->id ? 'selected' : ''); ?>>
                                                    <?php echo e($femea->matricula); ?> (<?php echo e($femea->tipoAve->nome ?? 'N/A'); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['femea_id'];
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
                                        <label for="data_inicio">Data de Início</label>
                                        <input type="date" name="data_inicio" class="form-control <?php $__errorArgs = ['data_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_inicio" value="<?php echo e(old('data_inicio')); ?>" required>
                                        <?php $__errorArgs = ['data_inicio'];
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
                                        <label for="data_fim">Data de Fim (Opcional)</label>
                                        <input type="date" name="data_fim" class="form-control <?php $__errorArgs = ['data_fim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_fim" value="<?php echo e(old('data_fim')); ?>">
                                        <?php $__errorArgs = ['data_fim'];
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
                                        <label for="observacoes">Observações (Opcional)</label>
                                        <textarea name="observacoes" class="form-control <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="observacoes" rows="3"><?php echo e(old('observacoes')); ?></textarea>
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
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Acasalamento</button>
                                    <a href="<?php echo e(route('acasalamentos.index')); ?>" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoAveRadios = document.querySelectorAll('input[name="selected_tipo_ave_id"]');
            const machoSelect = document.getElementById('macho_id');
            const femeaSelect = document.getElementById('femea_id');

            // Armazena todas as opções originais dos selects de macho e fêmea
            const allMachoOptions = Array.from(machoSelect.options);
            const allFemeaOptions = Array.from(femeaSelect.options);

            // Função para filtrar os dropdowns de macho e fêmea
            function filterAvesByTipoAve() {
                let selectedTipoAveId = null;
                tipoAveRadios.forEach(radio => {
                    if (radio.checked) {
                        selectedTipoAveId = radio.value;
                    }
                });

                // Limpa os selects, mantendo a opção padrão "Selecione..."
                machoSelect.innerHTML = '';
                machoSelect.appendChild(allMachoOptions[0].cloneNode(true)); // Clona para não remover do original
                femeaSelect.innerHTML = '';
                femeaSelect.appendChild(allFemeaOptions[0].cloneNode(true));

                // Se nenhuma espécie for selecionada, não adiciona nenhuma ave
                if (!selectedTipoAveId) {
                    return;
                }

                // Filtra e adiciona as aves machos
                allMachoOptions.forEach(option => {
                    if (option.value === "") return; // Ignora a opção padrão

                    if (option.dataset.tipoAveId === selectedTipoAveId) {
                        machoSelect.appendChild(option.cloneNode(true));
                    }
                });

                // Filtra e adiciona as aves fêmeas
                allFemeaOptions.forEach(option => {
                    if (option.value === "") return; // Ignora a opção padrão

                    if (option.dataset.tipoAveId === selectedTipoAveId) {
                        femeaSelect.appendChild(option.cloneNode(true));
                    }
                });

                // Tenta re-selecionar os valores antigos após o filtro
                const oldMachoId = "<?php echo e(old('macho_id')); ?>";
                const oldFemeaId = "<?php echo e(old('femea_id')); ?>";

                if (oldMachoId) {
                    const foundMachoOption = Array.from(machoSelect.options).find(opt => opt.value === oldMachoId);
                    if (foundMachoOption) {
                        machoSelect.value = oldMachoId;
                    } else {
                        machoSelect.value = ""; // Limpa se o valor antigo não estiver mais disponível
                    }
                }
                if (oldFemeaId) {
                    const foundFemeaOption = Array.from(femeaSelect.options).find(opt => opt.value === oldFemeaId);
                    if (foundFemeaOption) {
                        femeaSelect.value = oldFemeaId;
                    } else {
                        femeaSelect.value = ""; // Limpa se o valor antigo não estiver mais disponível
                    }
                }
            }

            // Adiciona ouvintes de evento aos rádio buttons
            tipoAveRadios.forEach(radio => {
                radio.addEventListener('change', filterAvesByTipoAve);
            });

            // Chama a função uma vez ao carregar a página para aplicar o filtro inicial
            filterAvesByTipoAve();
        });
    </script>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/acasalamentos/criar.blade.php ENDPATH**/ ?>