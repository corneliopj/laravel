


<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        
        <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="content-wrapper px-4 py-2" style="min-height: 797px;"> 
            
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Gestão de Receitas</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo e(url('/dashboard')); ?>">Home</a></li> 
                                <li class="breadcrumb-item active">Receitas</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="content">
                <div class="container-fluid">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-8">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                            <a href="<?php echo e(route('receitas.create')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                                + Nova Receita
                            </a>
                        </div>

                        
                        <?php if(session('success')): ?>
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                                <strong class="font-bold">Sucesso!</strong>
                                <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                                        <title>Fechar</title>
                                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.697l-2.651 2.652a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 0 1 1.697-1.697L10 8.303l2.651-2.652a1.2 1.2 0 0 1 1.697 1.697L11.697 10l2.652 2.651a1.2 1.2 0 0 1 0 1.698z"/>
                                    </svg>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if(session('error')): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                                <strong class="font-bold">Erro!</strong>
                                <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                                        <title>Fechar</title>
                                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.697l-2.651 2.652a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 0 1 1.697-1.697L10 8.303l2.651-2.652a1.2 1.2 0 0 1 1.697 1.697L11.697 10l2.652 2.651a1.2 1.2 0 0 1 0 1.698z"/>
                                    </svg>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if($receitas->isEmpty()): ?>
                            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center">
                                <p>Nenhuma receita cadastrada ainda. Comece adicionando uma!</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                                ID
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                                Descrição
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                                Origem
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                                Valor
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                                Data de Recebimento
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                                Ações
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <?php $__currentLoopData = $receitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                    <?php echo e($receita->id); ?>

                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                    <?php echo e($receita->descricao); ?>

                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                    <?php echo e(ucfirst($receita->origem)); ?> 
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                    R$ <?php echo e(number_format($receita->valor, 2, ',', '.')); ?>

                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                    <?php echo e(\Carbon\Carbon::parse($receita->data_recebimento)->format('d/m/Y')); ?>

                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-center space-x-2">
                                                    <a href="<?php echo e(route('receitas.show', $receita->id)); ?>" class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out" title="Ver Detalhes">
                                                        <i class="fas fa-eye"></i> 
                                                    </a>
                                                    <a href="<?php echo e(route('receitas.edit', $receita->id)); ?>" class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out" title="Editar">
                                                        <i class="fas fa-edit"></i> 
                                                    </a>
                                                    <form action="<?php echo e(route('receitas.destroy', $receita->id)); ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta receita?');" class="inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out" title="Excluir">
                                                            <i class="fas fa-trash-alt"></i> 
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        

    </div>
    

    
    <?php echo $__env->yieldPushContent('scripts'); ?> 

    
</body>
</html>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/receitas/listar.blade.php ENDPATH**/ ?>