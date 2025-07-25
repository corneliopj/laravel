@php
    $pageTitle = 'Listagem de Posturas de Ovos';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')
    {{-- Inclui o partial sidebar --}}
    @include('layouts.partials.sidebar')

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Listagem de Posturas de Ovos</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Posturas de Ovos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        {{-- Exibe mensagens de sucesso (flash) --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Exibe mensagens de erro (flash) --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="card-body">
                            {{-- Botão Nova Postura --}}
                            <div class="mb-3">
                                <a href="{{ route('posturas_ovos.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Nova Postura de Ovos
                                </a>
                            </div>

                            {{-- Filtro por Acasalamento --}}
                            <div class="form-group">
                                <label for="filter_acasalamento">Filtrar por Acasalamento:</label>
                                <select id="filter_acasalamento" class="form-control">
                                    <option value="">Todos os Acasalamentos</option>
                                    @foreach ($acasalamentos as $acasalamento)
                                        <option value="{{ $acasalamento->id }}" {{ $acasalamentoId == $acasalamento->id ? 'selected' : '' }}>
                                            Macho: {{ $acasalamento->macho->matricula ?? 'N/A' }} / Fêmea: {{ $acasalamento->femea->matricula ?? 'N/A' }} (Início: {{ $acasalamento->data_inicio->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <table class="table table-bordered table-striped" id="posturas-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Acasalamento</th>
                                        <th>Data Início Postura</th>
                                        <th>Data Fim Postura</th>
                                        <th>Total Ovos</th>
                                        <th>Observações</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($posturasOvos as $postura)
                                        <tr data-postura-id="{{ $postura->id }}">
                                            <td>{{ $postura->id }}</td>
                                            <td>
                                                M: {{ $postura->acasalamento->macho->matricula ?? 'N/A' }} / F: {{ $postura->acasalamento->femea->matricula ?? 'N/A' }}
                                                <br><small>({{ $postura->acasalamento->data_inicio->format('d/m/Y') }})</small>
                                            </td>
                                            <td>{{ $postura->data_inicio_postura->format('d/m/Y') }}</td>
                                            <td>{{ $postura->data_fim_postura ? $postura->data_fim_postura->format('d/m/Y') : 'Em andamento' }}</td>
                                            <td class="quantidade-ovos-cell">{{ $postura->quantidade_ovos }}</td>
                                            <td>{{ $postura->observacoes ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('posturas_ovos.edit', $postura->id) }}" class="btn btn-sm btn-primary" title="Editar Postura">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- Botão para adicionar ovos (visível apenas se a postura estiver em andamento) --}}
                                                @if (!$postura->data_fim_postura)
                                                <button type="button" class="btn btn-sm btn-info toggle-increment-form" data-id="{{ $postura->id }}" title="Adicionar Ovos">
                                                    <i class="fas fa-plus-circle"></i>
                                                </button>
                                                @endif
                                                
                                                {{-- Botão "Encerrar Postura" (visível apenas se a postura estiver em andamento) --}}
                                                @if (!$postura->data_fim_postura)
                                                <button type="button" class="btn btn-sm btn-success encerrar-postura-btn" data-id="{{ $postura->id }}" title="Encerrar Postura e Iniciar Incubação">
                                                    <i class="fas fa-check-square"></i> Encerrar
                                                </button>
                                                @endif

                                                {{-- Botão "Excluir Postura" (visível apenas se a postura JÁ ESTIVER ENCERRADA) --}}
                                                @if ($postura->data_fim_postura)
                                                <form action="{{ route('posturas_ovos.destroy', $postura->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir Postura" onclick="return confirm('Tem certeza que deseja excluir esta postura?');">
                                                        <i class="fas fa-trash"></i> Excluir
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        {{-- Sub-linha para o formulário de incremento --}}
                                        <tr class="increment-form-row" id="increment-form-row-{{ $postura->id }}" style="display:none;">
                                            <td colspan="7">
                                                <div class="card card-info card-outline mb-0">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Adicionar Ovos para Postura #{{ $postura->id }}</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group row">
                                                            <label for="quantidade_adicionar_{{ $postura->id }}" class="col-sm-2 col-form-label">Qtd. a Adicionar:</label>
                                                            <div class="col-sm-3">
                                                                <input type="number" class="form-control form-control-sm" id="quantidade_adicionar_{{ $postura->id }}" value="1" min="1">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <button type="button" class="btn btn-sm btn-success increment-eggs" data-id="{{ $postura->id }}">
                                                                    <i class="fas fa-plus"></i> Adicionar
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="alert alert-success alert-dismissible fade show increment-success-message" role="alert" style="display:none;">
                                                            Ovos adicionados com sucesso!
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="alert alert-danger alert-dismissible fade show increment-error-message" role="alert" style="display:none;">
                                                            Erro ao adicionar ovos.
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7">Nenhuma postura de ovos registada.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para Encerrar Postura --}}
    <div class="modal fade" id="encerrarPosturaModal" tabindex="-1" role="dialog" aria-labelledby="encerrarPosturaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="encerrarPosturaModalLabel">Encerrar Postura de Ovos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEncerrarPostura" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="postura_id" id="modal_postura_id">
                        <div class="form-group">
                            <label for="modal_data_fim_postura">Data de Fim da Postura:</label>
                            <input type="date" name="data_fim_postura" id="modal_data_fim_postura" class="form-control" required>
                        </div>
                        <p class="text-muted">Ao encerrar esta postura, uma nova incubação será criada automaticamente com a quantidade total de ovos desta postura e a data de fim informada.</p>
                        <div class="alert alert-danger" id="modal-error-message" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Encerrar e Iniciar Incubação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lógica para o filtro de acasalamento
            const filterAcasalamentoSelect = document.getElementById('filter_acasalamento');
            filterAcasalamentoSelect.addEventListener('change', function() {
                const selectedAcasalamentoId = this.value;
                let url = "{{ route('posturas_ovos.index') }}";
                if (selectedAcasalamentoId) {
                    url += `?acasalamento_id=${selectedAcasalamentoId}`;
                }
                window.location.href = url;
            });

            // Lógica para exibir/ocultar o formulário de incremento
            document.querySelectorAll('.toggle-increment-form').forEach(button => {
                button.addEventListener('click', function() {
                    const posturaId = this.dataset.id;
                    const incrementFormRow = document.getElementById(`increment-form-row-${posturaId}`);
                    // Esconde todas as outras sub-linhas abertas
                    document.querySelectorAll('.increment-form-row').forEach(row => {
                        if (row.id !== `increment-form-row-${posturaId}`) {
                            row.style.display = 'none';
                        }
                    });
                    // Alterna a visibilidade da sub-linha clicada
                    if (incrementFormRow.style.display === 'none') {
                        incrementFormRow.style.display = 'table-row';
                    } else {
                        incrementFormRow.style.display = 'none';
                    }
                });
            });

            // Lógica para o botão de incremento de ovos
            document.querySelectorAll('.increment-eggs').forEach(button => {
                button.addEventListener('click', function() {
                    const posturaId = this.dataset.id;
                    const quantidadeInput = document.getElementById(`quantidade_adicionar_${posturaId}`);
                    const quantidade = parseInt(quantidadeInput.value);
                    const successMessage = document.querySelector(`#increment-form-row-${posturaId} .increment-success-message`);
                    const errorMessage = document.querySelector(`#increment-form-row-${posturaId} .increment-error-message`);
                    const quantidadeOvosCell = document.querySelector(`tr[data-postura-id="${posturaId}"] .quantidade-ovos-cell`);

                    // Esconde mensagens anteriores
                    successMessage.style.display = 'none';
                    errorMessage.style.display = 'none';

                    if (isNaN(quantidade) || quantidade < 1) {
                        errorMessage.textContent = 'Por favor, insira uma quantidade válida (mínimo 1).';
                        errorMessage.style.display = 'block';
                        return;
                    }

                    // Requisição AJAX para incrementar a quantidade de ovos
                    fetch(`{{ url('posturas_ovos') }}/${posturaId}/increment-ovos`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Inclui o token CSRF
                        },
                        body: JSON.stringify({ quantidade: quantidade })
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Se a resposta não for OK (status 4xx ou 5xx), tenta ler o erro do JSON
                            return response.json().then(err => { throw new Error(err.error || 'Erro desconhecido ao incrementar ovos.'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            successMessage.textContent = data.success;
                            successMessage.style.display = 'block';
                            quantidadeOvosCell.textContent = data.new_quantity; // Atualiza a quantidade na tabela
                            quantidadeInput.value = 1; // Reseta o input para 1
                            // Opcional: Esconder o formulário após sucesso
                            // document.getElementById(`increment-form-row-${posturaId}`).style.display = 'none';
                        } else {
                            errorMessage.textContent = data.error || 'Erro ao incrementar ovos.';
                            errorMessage.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Erro na requisição AJAX:', error);
                        errorMessage.textContent = error.message || 'Erro de rede ou servidor ao incrementar ovos.';
                        errorMessage.style.display = 'block';
                    });
                });
            });

            // Lógica para o botão "Encerrar Postura"
            document.querySelectorAll('.encerrar-postura-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const posturaId = this.dataset.id;
                    document.getElementById('modal_postura_id').value = posturaId;
                    // Preenche a data de fim com a data atual por padrão
                    document.getElementById('modal_data_fim_postura').valueAsDate = new Date();
                    $('#encerrarPosturaModal').modal('show'); // Abre o modal
                });
            });

            // Lógica para submeter o formulário do modal de Encerrar Postura
            document.getElementById('formEncerrarPostura').addEventListener('submit', function(event) {
                event.preventDefault(); // Impede a submissão padrão do formulário

                const posturaId = document.getElementById('modal_postura_id').value;
                const dataFimPostura = document.getElementById('modal_data_fim_postura').value;
                const modalErrorMessage = document.getElementById('modal-error-message');

                modalErrorMessage.style.display = 'none'; // Esconde mensagens de erro anteriores

                fetch(`{{ url('posturas_ovos') }}/${posturaId}/encerrar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ data_fim_postura: dataFimPostura })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Erro desconhecido ao encerrar postura.'); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        $('#encerrarPosturaModal').modal('hide'); // Fecha o modal
                        window.location.reload(); // Recarrega a página para mostrar as mudanças
                    } else {
                        modalErrorMessage.textContent = data.error || 'Erro ao encerrar postura.';
                        modalErrorMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição AJAX de encerramento:', error);
                    modalErrorMessage.textContent = error.message || 'Erro de rede ou servidor ao encerrar postura.';
                    modalErrorMessage.style.display = 'block';
                });
            });
        });
    </script>
</div>
