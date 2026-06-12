<?php
    $pageTitle = 'Registrar Nova Incubação';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Registrar Nova Incubação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('incubacoes.index')); ?>">Incubações</a></li>
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
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados da Incubação</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action="<?php echo e(route('incubacoes.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="card-body">
                                    
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

                                    <div class="form-group">
                                        <label for="tipo_ave_id">Tipo de Ave</label>
                                        <select name="tipo_ave_id" id="tipo_ave_id" class="form-control <?php $__errorArgs = ['tipo_ave_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <option value="">Selecione um Tipo de Ave</option>
                                            <?php $__currentLoopData = $tiposAves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipoAve): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($tipoAve->id); ?>" <?php echo e(old('tipo_ave_id') == $tipoAve->id ? 'selected' : ''); ?> data-tempo-eclosao="<?php echo e($tipoAve->tempo_eclosao ?? 0); ?>"><?php echo e($tipoAve->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['tipo_ave_id'];
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
                                        <label for="lote_ovos_id">Lote de Ovos (Opcional)</label>
                                        <select name="lote_ovos_id" id="lote_ovos_id" class="form-control <?php $__errorArgs = ['lote_ovos_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <option value="">Selecione um Lote de Ovos</option>
                                            <?php $__currentLoopData = $lotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($lote->id); ?>" <?php echo e(old('lote_ovos_id') == $lote->id ? 'selected' : ''); ?>><?php echo e($lote->identificacao_lote); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['lote_ovos_id'];
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
                                        <label for="postura_ovo_id">Postura de Ovo (Opcional)</label>
                                        <select name="postura_ovo_id" id="postura_ovo_id" class="form-control <?php $__errorArgs = ['postura_ovo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <option value="">Selecione uma Postura de Ovo</option>
                                            <?php $__currentLoopData = $posturasOvos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $posturaOvo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                
                                                <option value="<?php echo e($posturaOvo->id); ?>" <?php echo e(old('postura_ovo_id') == $posturaOvo->id ? 'selected' : ''); ?>><?php echo e(optional($posturaOvo->data_inicio_postura)->format('d/m/Y') ?? 'N/A'); ?> - <?php echo e($posturaOvo->acasalamento->macho->tipoAve->nome ?? 'N/A'); ?> (<?php echo e($posturaOvo->quantidade_ovos); ?> ovos)</option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['postura_ovo_id'];
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
                                        <label for="data_entrada_incubadora">Data de Entrada na Incubadora</label>
                                        <input type="date" name="data_entrada_incubadora" id="data_entrada_incubadora" class="form-control <?php $__errorArgs = ['data_entrada_incubadora'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('data_entrada_incubadora', date('Y-m-d'))); ?>" required>
                                        <?php $__errorArgs = ['data_entrada_incubadora'];
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
                                        <label for="data_prevista_eclosao">Data Prevista de Eclosão</label>
                                        <input type="date" name="data_prevista_eclosao" id="data_prevista_eclosao" class="form-control <?php $__errorArgs = ['data_prevista_eclosao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('data_prevista_eclosao')); ?>" required readonly>
                                        <?php $__errorArgs = ['data_prevista_eclosao'];
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
                                        <label for="quantidade_ovos">Quantidade de Ovos</label>
                                        <input type="number" name="quantidade_ovos" id="quantidade_ovos" class="form-control <?php $__errorArgs = ['quantidade_ovos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('quantidade_ovos')); ?>" min="1" required>
                                        <?php $__errorArgs = ['quantidade_ovos'];
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
                                        <label for="chocadeira">Chocadeira</label>
                                        <input type="text" name="chocadeira" id="chocadeira" class="form-control <?php $__errorArgs = ['chocadeira'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('chocadeira')); ?>" placeholder="Nome ou identificação da chocadeira">
                                        <?php $__errorArgs = ['chocadeira'];
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
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('observacoes')); ?></textarea>
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

                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="hidden" name="ativo" value="0">
                                            <input type="checkbox" name="ativo" id="ativo" class="form-check-input" value="1" <?php echo e(old('ativo', 1) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="ativo">Ativo</label>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Registrar Incubação</button>
                                    <a href="<?php echo e(route('incubacoes.index')); ?>" class="btn btn-secondary">Cancelar</a>
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
            const tipoAveSelect = document.getElementById('tipo_ave_id');
            const dataEntradaInput = document.getElementById('data_entrada_incubadora');
            const dataPrevistaEclosaoInput = document.getElementById('data_prevista_eclosao');

            let tempoEclosaoDias = 0; // Variável para armazenar o tempo de eclosão

            // Função para calcular e preencher a data prevista de eclosão
            function calcularDataPrevistaEclosao() {
                const dataEntradaStr = dataEntradaInput.value;
                if (!dataEntradaStr || tempoEclosaoDias === 0) {
                    dataPrevistaEclosaoInput.value = '';
                    return;
                }

                const dataEntrada = new Date(dataEntradaStr + 'T00:00:00');
                if (isNaN(dataEntrada.getTime())) {
                    dataPrevistaEclosaoInput.value = '';
                    return;
                }

                const dataPrevista = new Date(dataEntrada);
                dataPrevista.setDate(dataEntrada.getDate() + tempoEclosaoDias);

                const year = dataPrevista.getFullYear();
                const month = String(dataPrevista.getMonth() + 1).padStart(2, '0');
                const day = String(dataPrevista.getDate()).padStart(2, '0');
                dataPrevistaEclosaoInput.value = `${year}-${month}-${day}`;
            }

            // Listener para o campo Tipo de Ave
            tipoAveSelect.addEventListener('change', async function() {
                const selectedOption = this.options[this.selectedIndex];
                const tipoAveId = selectedOption.value;

                if (tipoAveId) {
                    const tempoEclosaoAttr = selectedOption.dataset.tempoEclosao;
                    if (tempoEclosaoAttr) {
                        tempoEclosaoDias = parseInt(tempoEclosaoAttr);
                        console.log(`Tempo de eclosão obtido do atributo: ${tempoEclosaoDias} dias.`);
                        calcularDataPrevistaEclosao();
                    } else {
                        try {
                            const response = await fetch(`/tipos_aves/${tipoAveId}/tempo-eclosao`);
                            if (!response.ok) {
                                throw new Error('Erro ao buscar tempo de eclosão.');
                            }
                            const data = await response.json();
                            tempoEclosaoDias = data.tempo_eclosao || 0;
                            console.log(`Tempo de eclosão obtido via AJAX: ${tempoEclosaoDias} dias.`);
                            calcularDataPrevistaEclosao();
                        } catch (error) {
                            console.error('Erro:', error);
                            tempoEclosaoDias = 0;
                            dataPrevistaEclosaoInput.value = '';
                            alert('Não foi possível carregar o tempo de eclosão para o tipo de ave selecionado.');
                        }
                    }
                } else {
                    tempoEclosaoDias = 0;
                    dataPrevistaEclosaoInput.value = '';
                }
            });

            // Listener para o campo Data de Entrada na Incubadora
            dataEntradaInput.addEventListener('change', calcularDataPrevistaEclosao);

            if (tipoAveSelect.value) {
                const initialSelectedOption = tipoAveSelect.options[tipoAveSelect.selectedIndex];
                const initialTempoEclosaoAttr = initialSelectedOption.dataset.tempoEclosao;
                if (initialTempoEclosaoAttr) {
                    tempoEclosaoDias = parseInt(initialTempoEclosaoAttr);
                }
                calcularDataPrevistaEclosao();
            } else {
                calcularDataPrevistaEclosao();
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/incubacoes/create.blade.php ENDPATH**/ ?>