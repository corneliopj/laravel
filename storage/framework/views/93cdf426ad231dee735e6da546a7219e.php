<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?php echo e(url('/')); ?>" class="nav-link">Home</a>
        </li>
        
    </ul>

    <form class="form-inline ml-3" action="<?php echo e(route('aves.search')); ?>" method="GET" id="searchForm">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Buscar Aves..." aria-label="Search" name="query" id="searchInput" autocomplete="off">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="searchResults" class="list-group position-absolute bg-white border rounded shadow-sm" style="z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto; top: 100%; left: 0; display: none;">
                </div>
        </div>
    </form>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <form action="<?php echo e(route('logout')); ?>" method="POST" class="form-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-link nav-link">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </form>
        </li>
    </ul>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const searchResultsContainer = document.getElementById('searchResults'); // Renomeado para consistência
        let debounceTimer;

        // Função para esconder as sugestões
        function hideSuggestions() {
            searchResultsContainer.innerHTML = ''; // Limpa as sugestões ao esconder
            searchResultsContainer.style.display = 'none';
        }

        // Evento de input para buscar sugestões
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer); // Limpa o timer anterior
            const query = this.value.trim(); // Pega o valor e remove espaços em branco

            if (query.length < 2) { // Não busca se a query for muito curta
                hideSuggestions();
                return;
            }

            debounceTimer = setTimeout(() => { // Espera um pouco antes de fazer a busca
                // Faz a requisição AJAX para a rota de sugestões
                fetch(`<?php echo e(route('aves.searchSuggestions')); ?>?query=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        searchResultsContainer.innerHTML = ''; // Limpa as sugestões anteriores
                        if (data.length > 0) {
                            data.forEach(ave => {
    const suggestionItem = document.createElement('a');
    suggestionItem.href = `<?php echo e(route('aves.show', ':aveId')); ?>`.replace(':aveId', ave.id);
    suggestionItem.classList.add('list-group-item', 'list-group-item-action');
    // AGORA SIM: Usa ave.matricula e ave.tipo_ave_nome (a nova chave do backend)
    suggestionItem.textContent = `${ave.matricula} - ${ave.tipo_ave_nome}`;
    suggestionItem.dataset.aveId = ave.id;
    searchResultsContainer.appendChild(suggestionItem);
});
                            searchResultsContainer.style.display = 'block'; // Mostra o contêiner
                        } else {
                            hideSuggestions(); // Esconde se não houver sugestões
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar sugestões:', error);
                        hideSuggestions(); // Esconde em caso de erro
                    });
            }, 300); // Debounce de 300ms
        });

        // Evento de clique no contêiner de sugestões (delegação de eventos)
        // O redirecionamento ocorrerá automaticamente pelo href do 'a' tag.
        searchResultsContainer.addEventListener('click', function (event) {
            // Verifica se o clique foi num item de sugestão (elemento 'a' com a classe list-group-item)
            if (event.target.classList.contains('list-group-item')) {
                // O navegador já vai seguir o 'href' do link.
                // Apenas preenche o input e esconde as sugestões.
                searchInput.value = event.target.textContent;
                hideSuggestions();
            }
        });

        // Esconder sugestões quando clicar fora do input ou das sugestões
        document.addEventListener('click', function (event) {
            // Verifica se o clique não foi no campo de busca nem dentro do contêiner de sugestões
            if (!searchInput.contains(event.target) && !searchResultsContainer.contains(event.target)) {
                hideSuggestions();
            }
        });

        // Adiciona um evento para esconder as sugestões ao pressionar 'Escape'
        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideSuggestions();
            }
        });
    });
</script>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/layouts/partials/navbar.blade.php ENDPATH**/ ?>