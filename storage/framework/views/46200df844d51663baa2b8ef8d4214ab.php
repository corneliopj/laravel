 

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Detalhes da Receita</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">ID:</p>
                <p class="mt-1 text-lg text-gray-900 dark:text-white"><?php echo e($receita->id); ?></p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Descrição:</p>
                <p class="mt-1 text-lg text-gray-900 dark:text-white"><?php echo e($receita->descricao); ?></p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Origem:</p>
                <p class="mt-1 text-lg text-gray-900 dark:text-white"><?php echo e(ucfirst($receita->origem)); ?></p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Valor:</p>
                <p class="mt-1 text-lg text-gray-900 dark:text-white">R$ <?php echo e(number_format($receita->valor, 2, ',', '.')); ?></p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Data de Recebimento:</p>
                <p class="mt-1 text-lg text-gray-900 dark:text-white"><?php echo e(\Carbon\Carbon::parse($receita->data_recebimento)->format('d/m/Y')); ?></p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Criado em:</p>
                <p class="mt-1 text-lg text-gray-900 dark:text-white"><?php echo e($receita->created_at->format('d/m/Y H:i')); ?></p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Última Atualização:</p>
                <p class="mt-1 text-lg text-gray-900 dark:text-white"><?php echo e($receita->updated_at->format('d/m/Y H:i')); ?></p>
            </div>
        </div>

        <?php if($receita->observacoes): ?>
            <div class="mt-6">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Observações:</p>
                <p class="mt-1 text-lg text-gray-900 dark:text-white"><?php echo e($receita->observacoes); ?></p>
            </div>
        <?php endif; ?>

        <div class="mt-8 flex flex-col sm:flex-row justify-start space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="<?php echo e(route('receitas.edit', $receita->id)); ?>" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
            <form action="<?php echo e(route('receitas.destroy', $receita->id)); ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta receita?');" class="inline-block">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                    <i class="fas fa-trash-alt mr-2"></i> Excluir
                </button>
            </form>
            <a href="<?php echo e(route('receitas.index')); ?>" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i> Voltar para a Lista
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/despesas/listar.blade.php ENDPATH**/ ?>