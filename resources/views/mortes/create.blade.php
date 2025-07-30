@php
    $pageTitle = 'Registrar Nova Morte';
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
                        <h1>Registrar Nova Morte</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('mortes.index') }}">Mortes</a></li>
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
                            <form action="{{ route('mortes.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Tipo de Registro</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipo_registro" id="tipo_individual" value="individual" {{ old('tipo_registro', $preSelectedAveId ? 'individual' : ($preSelectedPlantelId ? 'plantel' : 'individual')) == 'individual' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tipo_individual">Ave Individual</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipo_registro" id="tipo_plantel" value="plantel" {{ old('tipo_registro', $preSelectedAveId ? 'individual' : ($preSelectedPlantelId ? 'plantel' : 'individual')) == 'plantel' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tipo_plantel">Plantel Agrupado</label>
                                        </div>
                                        @error('tipo_registro')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div id="div_ave_id" class="form-group" style="{{ old('tipo_registro', $preSelectedAveId ? 'individual' : ($preSelectedPlantelId ? 'plantel' : 'individual')) == 'individual' ? '' : 'display:none;' }}">
                                        <label for="ave_id">Ave Individual</label>
                                        <select name="ave_id" id="ave_id" class="form-control @error('ave_id') is-invalid @enderror">
                                            <option value="">Selecione uma Ave</option>
                                            @foreach($aves as $ave)
                                                <option value="{{ $ave->id }}" {{ old('ave_id', $preSelectedAveId) == $ave->id ? 'selected' : '' }}>{{ $ave->matricula }} ({{ $ave->tipoAve->nome ?? 'N/A' }})</option>
                                            @endforeach
                                        </select>
                                        @error('ave_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div id="div_plantel_id" style="{{ old('tipo_registro', $preSelectedAveId ? 'individual' : ($preSelectedPlantelId ? 'plantel' : 'individual')) == 'plantel' ? '' : 'display:none;' }}">
                                        <div class="form-group">
                                            <label for="plantel_id">Plantel Agrupado</label>
                                            <select name="plantel_id" id="plantel_id" class="form-control @error('plantel_id') is-invalid @enderror">
                                                <option value="">Selecione um Plantel</option>
                                                @foreach($plantelOptions as $plantel)
                                                    <option value="{{ $plantel->id }}" {{ old('plantel_id', $preSelectedPlantelId) == $plantel->id ? 'selected' : '' }}>{{ $plantel->identificacao_grupo }} (Qtd. Atual: {{ $plantel->quantidade_atual }})</option>
                                                @endforeach
                                            </select>
                                            @error('plantel_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="quantidade_mortes_plantel">Quantidade de Aves Mortas no Plantel</label>
                                            <input type="number" name="quantidade_mortes_plantel" id="quantidade_mortes_plantel" class="form-control @error('quantidade_mortes_plantel') is-invalid @enderror" value="{{ old('quantidade_mortes_plantel') }}" min="1">
                                            @error('quantidade_mortes_plantel')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="data_morte">Data da Morte</label>
                                        <input type="date" name="data_morte" id="data_morte" class="form-control @error('data_morte') is-invalid @enderror" value="{{ old('data_morte', date('Y-m-d')) }}" required>
                                        @error('data_morte')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="causa_morte">Causa da Morte</label>
                                        <input type="text" name="causa_morte" id="causa_morte" class="form-control @error('causa_morte') is-invalid @enderror" value="{{ old('causa_morte') }}" placeholder="Ex: Doença, Predador, Acidente">
                                        @error('causa_morte')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3" placeholder="Detalhes adicionais sobre a morte..."></textarea>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-danger">Registrar Morte</button>
                                    <a href="{{ route('mortes.index') }}" class="btn btn-secondary">Cancelar</a>
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
</div>
<!-- ./wrapper -->
