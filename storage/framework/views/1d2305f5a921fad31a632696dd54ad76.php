<?php
    $pageTitle = 'Editar Movimentação de Plantel';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Editar Movimentação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('plantel.index')); ?>">Plantéis</a></li>
                            <?php if($movimentacaoPlantel->plantel): ?>
                                <li class="breadcrumb-item"><a href="<?php echo e(route('plantel.show', $movimentacaoPlantel->plantel->id)); ?>">Detalhes do Plantel</a></li>
                            <?php endif; ?>
                            <li class="breadcrumb-item active">Editar Movimentação</li>
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
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Editar Dados da Movimentação</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action="<?php echo e(route('movimentacoes-plantel.update', $movimentacaoPlantel->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="plantel_id">Plantel</label>
                                        <select name="plantel_id" id="plantel_id" class="form-control <?php $__errorArgs = ['plantel_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="">Selecione um Plantel</option>
                                            <?php $__currentLoopData = $plantelOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plantel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($plantel->id); ?>"
                                                    <?php echo e(old('plantel_id', $movimentacaoPlantel->plantel_id) == $plantel->id ? 'selected' : ''); ?>>
                                                    <?php echo e($plantel->identificacao_grupo); ?> (Qtd. Atual: <?php echo e($plantel->quantidade_atual); ?>)
                                                </option>
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
                                        <label for="tipo_movimentacao">Tipo de Movimentação</label>
                                        <select name="tipo_movimentacao" id="tipo_movimentacao" class="form-control <?php $__errorArgs = ['tipo_movimentacao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="">Selecione o Tipo</option>
                                            <option value="entrada" <?php echo e(old('tipo_movimentacao', $movimentacaoPlantel->tipo_movimentacao) == 'entrada' ? 'selected' : ''); ?>>Entrada</option>
                                            <option value="saida_venda" <?php echo e(old('tipo_movimentacao', $movimentacaoPlantel->tipo_movimentacao) == 'saida_venda' ? 'selected' : ''); ?>>Saída (Venda)</option>
                                            <option value="saida_morte" <?php echo e(old('tipo_movimentacao', $movimentacaoPlantel->tipo_movimentacao) == 'saida_morte' ? 'selected' : ''); ?>>Saída (Morte)</option>
                                            <option value="saida_consumo" <?php echo e(old('tipo_movimentacao', $movimentacaoPlantel->tipo_movimentacao) == 'saida_consumo' ? 'selected' : ''); ?>>Saída (Consumo)</option>
                                            <option value="saida_doacao" <?php echo e(old('tipo_movimentacao', $movimentacaoPlantel->tipo_movimentacao) == 'saida_doacao' ? 'selected' : ''); ?>>Saída (Doação)</option>
                                            <option value="saida_descarte" <?php echo e(old('tipo_movimentacao', $movimentacaoPlantel->tipo_movimentacao) == 'saida_descarte' ? 'selected' : ''); ?>>Saída (Descarte)</option>
                                            <option value="outros" <?php echo e(old('tipo_movimentacao', $movimentacaoPlantel->tipo_movimentacao) == 'outros' ? 'selected' : ''); ?>>Outros</option>
                                        </select>
                                        <?php $__errorArgs = ['tipo_movimentacao'];
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
                                        <label for="quantidade">Quantidade</label>
                                        <input type="number" name="quantidade" id="quantidade" class="form-control <?php $__errorArgs = ['quantidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('quantidade', $movimentacaoPlantel->quantidade)); ?>" min="1" placeholder="Quantidade de aves" required>
                                        <?php $__errorArgs = ['quantidade'];
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
                                        <label for="data_movimentacao">Data da Movimentação</label>
                                        <input type="date" name="data_movimentacao" id="data_movimentacao" class="form-control <?php $__errorArgs = ['data_movimentacao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('data_movimentacao', $movimentacaoPlantel->data_movimentacao->format('Y-m-d'))); ?>" required>
                                        <?php $__errorArgs = ['data_movimentacao'];
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
unset($__errorArgs, $__bag); ?>" rows="3" placeholder="Notas sobre esta movimentação..."><?php echo e(old('observacoes', $movimentacaoPlantel->observacoes)); ?></textarea>
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
                                    <button type="submit" class="btn btn-primary">Atualizar Movimentação</button>
                                    <a href="<?php echo e(route('movimentacoes-plantel.show', $movimentacaoPlantel->id)); ?>" class="btn btn-secondary">Cancelar</a>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/movimentacoes_plantel/edit.blade.php ENDPATH**/ ?>