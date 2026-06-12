<?php
    $pageTitle = 'Registrar Nova Morte';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Registrar Nova Morte</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('mortes.index')); ?>">Mortes</a></li>
                            <li class="breadcrumb-item active">Registrar</li>
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
                        <div class="card card-danger">
                            <div class="card-header">
                                <h3 class="card-title">Dados da Morte</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action="<?php echo e(route('mortes.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Tipo de Registro</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipo_registro" id="tipo_individual" value="individual" <?php echo e(old('tipo_registro', $preSelectedAveId ? 'individual' : ($preSelectedPlantelId ? 'plantel' : 'individual')) == 'individual' ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="tipo_individual">Ave Individual</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipo_registro" id="tipo_plantel" value="plantel" <?php echo e(old('tipo_registro', $preSelectedAveId ? 'individual' : ($preSelectedPlantelId ? 'plantel' : 'individual')) == 'plantel' ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="tipo_plantel">Plantel Agrupado</label>
                                        </div>
                                        <?php $__errorArgs = ['tipo_registro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div id="div_ave_id" class="form-group" style="<?php echo e(old('tipo_registro', $preSelectedAveId ? 'individual' : ($preSelectedPlantelId ? 'plantel' : 'individual')) == 'individual' ? '' : 'display:none;'); ?>">
                                        <label for="ave_id">Ave Individual</label>
                                        <select name="ave_id" id="ave_id" class="form-control <?php $__errorArgs = ['ave_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <option value="">Selecione uma Ave</option>
                                            <?php $__currentLoopData = $aves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($ave->id); ?>" <?php echo e(old('ave_id', $preSelectedAveId) == $ave->id ? 'selected' : ''); ?>><?php echo e($ave->matricula); ?> (<?php echo e($ave->tipoAve->nome ?? 'N/A'); ?>)</option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['ave_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div id="div_plantel_id" style="<?php echo e(old('tipo_registro', $preSelectedAveId ? 'individual' : ($preSelectedPlantelId ? 'plantel' : 'individual')) == 'plantel' ? '' : 'display:none;'); ?>">
                                        <div class="form-group">
                                            <label for="plantel_id">Plantel Agrupado</label>
                                            <select name="plantel_id" id="plantel_id" class="form-control <?php $__errorArgs = ['plantel_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <option value="">Selecione um Plantel</option>
                                                <?php $__currentLoopData = $plantelOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plantel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($plantel->id); ?>" <?php echo e(old('plantel_id', $preSelectedPlantelId) == $plantel->id ? 'selected' : ''); ?>><?php echo e($plantel->identificacao_grupo); ?> (Qtd. Atual: <?php echo e($plantel->quantidade_atual); ?>)</option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php $__errorArgs = ['plantel_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="quantidade_mortes_plantel">Quantidade de Aves Mortas no Plantel</label>
                                            <input type="number" name="quantidade_mortes_plantel" id="quantidade_mortes_plantel" class="form-control <?php $__errorArgs = ['quantidade_mortes_plantel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('quantidade_mortes_plantel')); ?>" min="1">
                                            <?php $__errorArgs = ['quantidade_mortes_plantel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="data_morte">Data da Morte</label>
                                        <input type="date" name="data_morte" id="data_morte" class="form-control <?php $__errorArgs = ['data_morte'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('data_morte', date('Y-m-d'))); ?>" required>
                                        <?php $__errorArgs = ['data_morte'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="causa_morte">Causa da Morte</label>
                                        <input type="text" name="causa_morte" id="causa_morte" class="form-control <?php $__errorArgs = ['causa_morte'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('causa_morte')); ?>" placeholder="Ex: Doença, Predador, Acidente">
                                        <?php $__errorArgs = ['causa_morte'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea name="observacoes" id="observacoes" class="form-control <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" placeholder="Detalhes adicionais sobre a morte..."></textarea>
                                        <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-danger">Registrar Morte</button>
                                    <a href="<?php echo e(route('mortes.index')); ?>" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const tipoIndividualRadio = document.getElementById('tipo_individual');
            const tipoPlantelRadio = document.getElementById('tipo_plantel');
            const divAveId = document.getElementById('div_ave_id');
            const divPlantelId = document.getElementById('div_plantel_id');
            const aveIdSelect = document.getElementById('ave_id');
            const plantelIdSelect = document.getElementById('plantel_id');
            const quantidadeMortesPlantelInput = document.getElementById('quantidade_mortes_plantel');

            function toggleFields() {
                if (tipoIndividualRadio.checked) {
                    divAveId.style.display = 'block';
                    divPlantelId.style.display = 'none';
                    // Define 'required' para campos visíveis e remove dos invisíveis
                    aveIdSelect.setAttribute('required', 'required');
                    plantelIdSelect.removeAttribute('required');
                    quantidadeMortesPlantelInput.removeAttribute('required');
                } else {
                    divAveId.style.display = 'none';
                    divPlantelId.style.display = 'block';
                    // Define 'required' para campos visíveis e remove dos invisíveis
                    aveIdSelect.removeAttribute('required');
                    plantelIdSelect.setAttribute('required', 'required');
                    quantidadeMortesPlantelInput.setAttribute('required', 'required');
                }
            }

            // Adiciona listeners para os radios
            tipoIndividualRadio.addEventListener('change', toggleFields);
            tipoPlantelRadio.addEventListener('change', toggleFields);

            // Executa na carga da página para definir o estado inicial
            toggleFields();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/mortes/create.blade.php ENDPATH**/ ?>