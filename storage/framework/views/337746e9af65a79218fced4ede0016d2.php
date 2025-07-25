<?php
    $pageTitle = 'Adicionar Nova Ave';
?>


<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="wrapper">
    
    <div class="preloader flex-column justify-content-center align-items-center" style="height: 0px;">
        <img class="animation__shake" src="<?php echo e(asset('img/logo.png')); ?>" alt="AdminLTELogo" height="60" width="60" style="display: none;">
    </div>
    
    <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Adicionar Nova Ave</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('aves.index')); ?>">Aves</a></li>
                            <li class="breadcrumb-item active">Adicionar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        
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

                        
                        <?php if(session('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo e(session('error')); ?>

                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados da Ave</h3>
                            </div>
                            
                            <form action="<?php echo e(route('aves.store')); ?>" method="post" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?> 
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="matricula">Matrícula</label>
                                        <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Digite a matrícula da ave" value="<?php echo e(old('matricula')); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_ave_id">Tipo de Ave</label>
                                        <select class="form-control" id="tipo_ave_id" name="tipo_ave_id" required>
                                            <option value="">Selecione o tipo de ave</option>
                                            <?php $__empty_1 = true; $__currentLoopData = $tiposAves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <option value="<?php echo e($tipo->id); ?>" <?php echo e(old('tipo_ave_id') == $tipo->id ? 'selected' : ''); ?>><?php echo e($tipo->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <option value="">Nenhum tipo de ave disponível</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="variacao_id">Variação</label>
                                        <select class="form-control" id="variacao_id" name="variacao_id">
                                            <option value="">Selecione a variação (opcional)</option>
                                            
                                            <?php $__empty_1 = true; $__currentLoopData = $variacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <option value="<?php echo e($variacao->id); ?>" data-tipo-ave-id="<?php echo e($variacao->tipo_ave_id); ?>" <?php echo e(old('variacao_id') == $variacao->id ? 'selected' : ''); ?>><?php echo e($variacao->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <option value="">Nenhuma variação disponível</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="lote_id">Lote</label>
                                        <select class="form-control" id="lote_id" name="lote_id">
                                            <option value="">Selecione o lote (opcional)</option>
                                            <?php $__empty_1 = true; $__currentLoopData = $lotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <option value="<?php echo e($lote->id); ?>" <?php echo e(old('lote_id') == $lote->id ? 'selected' : ''); ?>><?php echo e($lote->identificacao_lote); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <option value="">Nenhum lote disponível</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="incubacao_id">Incubação (Origem)</label>
                                        <select class="form-control" id="incubacao_id" name="incubacao_id">
                                            <option value="">Selecione a incubação de origem (opcional)</option>
                                            <?php $__empty_1 = true; $__currentLoopData = $incubacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incubacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <option value="<?php echo e($incubacao->id); ?>" <?php echo e(old('incubacao_id') == $incubacao->id ? 'selected' : ''); ?>>Lote: <?php echo e($incubacao->loteOvos->identificacao_lote ?? 'N/A'); ?> - Entrada: <?php echo e($incubacao->data_entrada_incubadora->format('d/m/Y')); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <option value="">Nenhuma incubação disponível</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="data_eclosao">Data de Eclosão</label>
                                        <input type="date" class="form-control" id="data_eclosao" name="data_eclosao" required value="<?php echo e(old('data_eclosao')); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="sexo">Sexo</label>
                                        <select class="form-control" id="sexo" name="sexo">
                                            <option value="Nao identificado" <?php echo e(old('sexo') == 'Nao identificado' ? 'selected' : ''); ?>>Não identificado</option>
                                            <option value="Macho" <?php echo e(old('sexo') == 'Macho' ? 'selected' : ''); ?>>Macho</option>
                                            <option value="Femea" <?php echo e(old('sexo') == 'Femea' ? 'selected' : ''); ?>>Fêmea</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="vendavel" name="vendavel" value="1" <?php echo e(old('vendavel') ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="vendavel">Vendável</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="foto">Foto da Ave (Opcional)</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                                                <label class="custom-file-label" for="foto">Escolha um arquivo</label>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Formatos aceitos: Imagem. Tamanho máximo: 5MB. Será redimensionada para 500x500px (máx. 1MB).</small>
                                        <?php $__errorArgs = ['foto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Ave</button>
                                    <a href="<?php echo e(route('aves.index')); ?>" class="btn btn-secondary">Cancelar</a>
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
            const tipoAveSelect = document.getElementById('tipo_ave_id');
            const variacaoSelect = document.getElementById('variacao_id');
            const allVariacaoOptions = Array.from(variacaoSelect.options); // Converte para array para facilitar a manipulação

            function filterVariacoes() {
                const selectedTipoAveId = tipoAveSelect.value;
                
                // Limpa as opções atuais do select de variação, exceto a primeira (opção "Selecione...")
                variacaoSelect.innerHTML = '';
                variacaoSelect.appendChild(allVariacaoOptions[0]); // Adiciona a opção padrão de volta

                allVariacaoOptions.forEach(option => {
                    if (option.value === "") return; // Ignora a opção padrão

                    const tipoAveIdForOption = option.dataset.tipoAveId;

                    if (selectedTipoAveId === "" || tipoAveIdForOption === selectedTipoAveId) {
                        variacaoSelect.appendChild(option);
                    }
                });

                // Tenta pré-selecionar a variação se houver um old('variacao_id')
                const oldVariacaoId = "<?php echo e(old('variacao_id')); ?>";
                if (oldVariacaoId) {
                    const foundOption = allVariacaoOptions.find(option => option.value === oldVariacaoId);
                    if (foundOption && (foundOption.dataset.tipoAveId === selectedTipoAveId || selectedTipoAveId === "")) {
                        variacaoSelect.value = oldVariacaoId;
                    } else {
                        variacaoSelect.value = ""; // Limpa se a variação antiga não for compatível
                    }
                } else {
                    variacaoSelect.value = ""; // Garante que nada está selecionado se não houver old value
                }
            }

            // Adiciona o ouvinte de evento para o select de Tipo de Ave
            tipoAveSelect.addEventListener('change', filterVariacoes);

            // Chama a função uma vez ao carregar a página para aplicar o filtro inicial
            filterVariacoes();

            // Script para exibir o nome do arquivo selecionado no input custom-file
            document.getElementById('foto').addEventListener('change', function(e) {
                var fileName = e.target.files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        });
    </script>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/aves/criar.blade.php ENDPATH**/ ?>