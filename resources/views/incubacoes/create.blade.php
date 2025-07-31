@php
    $pageTitle = 'Registrar Nova Incubação';
@endphp

@include('layouts.partials.head')

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Registrar Nova Incubação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('incubacoes.index') }}">Incubações</a></li>
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
                            <form action="{{ route('incubacoes.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    {{-- Mensagens de sucesso/erro --}}
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="tipo_ave_id">Tipo de Ave</label>
                                        <select name="tipo_ave_id" id="tipo_ave_id" class="form-control @error('tipo_ave_id') is-invalid @enderror" required>
                                            <option value="">Selecione um Tipo de Ave</option>
                                            @foreach($tiposAves as $tipoAve)
                                                <option value="{{ $tipoAve->id }}" {{ old('tipo_ave_id') == $tipoAve->id ? 'selected' : '' }} data-tempo-eclosao="{{ $tipoAve->tempo_eclosao ?? 0 }}">{{ $tipoAve->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('tipo_ave_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="lote_ovos_id">Lote de Ovos (Opcional)</label>
                                        <select name="lote_ovos_id" id="lote_ovos_id" class="form-control @error('lote_ovos_id') is-invalid @enderror">
                                            <option value="">Selecione um Lote de Ovos</option>
                                            @foreach($lotes as $lote)
                                                <option value="{{ $lote->id }}" {{ old('lote_ovos_id') == $lote->id ? 'selected' : '' }}>{{ $lote->identificacao_lote }}</option>
                                            @endforeach
                                        </select>
                                        @error('lote_ovos_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="postura_ovo_id">Postura de Ovo (Opcional)</label>
                                        <select name="postura_ovo_id" id="postura_ovo_id" class="form-control @error('postura_ovo_id') is-invalid @enderror">
                                            <option value="">Selecione uma Postura de Ovo</option>
                                            @foreach($posturasOvos as $posturaOvo)
                                                {{-- CORREÇÃO AQUI: Usar optional() e data_inicio_postura --}}
                                                <option value="{{ $posturaOvo->id }}" {{ old('postura_ovo_id') == $posturaOvo->id ? 'selected' : '' }}>{{ optional($posturaOvo->data_inicio_postura)->format('d/m/Y') ?? 'N/A' }} - {{ $posturaOvo->acasalamento->macho->tipoAve->nome ?? 'N/A' }} ({{ $posturaOvo->quantidade_ovos }} ovos)</option>
                                            @endforeach
                                        </select>
                                        @error('postura_ovo_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="data_entrada_incubadora">Data de Entrada na Incubadora</label>
                                        <input type="date" name="data_entrada_incubadora" id="data_entrada_incubadora" class="form-control @error('data_entrada_incubadora') is-invalid @enderror" value="{{ old('data_entrada_incubadora', date('Y-m-d')) }}" required>
                                        @error('data_entrada_incubadora')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="data_prevista_eclosao">Data Prevista de Eclosão</label>
                                        <input type="date" name="data_prevista_eclosao" id="data_prevista_eclosao" class="form-control @error('data_prevista_eclosao') is-invalid @enderror" value="{{ old('data_prevista_eclosao') }}" required readonly>
                                        @error('data_prevista_eclosao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="quantidade_ovos">Quantidade de Ovos</label>
                                        <input type="number" name="quantidade_ovos" id="quantidade_ovos" class="form-control @error('quantidade_ovos') is-invalid @enderror" value="{{ old('quantidade_ovos') }}" min="1" required>
                                        @error('quantidade_ovos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="chocadeira">Chocadeira</label>
                                        <input type="text" name="chocadeira" id="chocadeira" class="form-control @error('chocadeira') is-invalid @enderror" value="{{ old('chocadeira') }}" placeholder="Nome ou identificação da chocadeira">
                                        @error('chocadeira')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3">{{ old('observacoes') }}</textarea>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="hidden" name="ativo" value="0">
                                            <input type="checkbox" name="ativo" id="ativo" class="form-check-input" value="1" {{ old('ativo', 1) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ativo">Ativo</label>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Registrar Incubação</button>
                                    <a href="{{ route('incubacoes.index') }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    @include('layouts.partials.scripts')
    @include('layouts.partials.footer')

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
</div>
<!-- ./wrapper -->
