@php
    $pageTitle = 'Editar Incubação';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')
    {{-- Inclui o partial sidebar --}}
    @include('layouts.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Editar Incubação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('incubacoes.index') }}">Incubações</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Formulário de Edição de Incubação</h3>
                            </div>
                            <!-- /.card-header -->
                            <form action="{{ route('incubacoes.update', $incubacao->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="lote_ovos_id">Lote de Ovos</label>
                                            <select class="form-control @error('lote_ovos_id') is-invalid @enderror" id="lote_ovos_id" name="lote_ovos_id" required>
                                                <option value="">Selecione um Lote</option>
                                                @foreach ($lotes as $lote)
                                                    <option value="{{ $lote->id }}" {{ old('lote_ovos_id', $incubacao->lote_ovos_id) == $lote->id ? 'selected' : '' }}>
                                                        {{ $lote->identificacao_lote }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('lote_ovos_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="tipo_ave_id">Tipo de Ave</label>
                                            <select class="form-control @error('tipo_ave_id') is-invalid @enderror" id="tipo_ave_id" name="tipo_ave_id" required>
                                                <option value="">Selecione um Tipo de Ave</option>
                                                @foreach ($tiposAves as $tipoAve)
                                                    <option value="{{ $tipoAve->id }}" {{ old('tipo_ave_id', $incubacao->tipo_ave_id) == $tipoAve->id ? 'selected' : '' }}>
                                                        {{ $tipoAve->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tipo_ave_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="data_entrada_incubadora">Data de Entrada na Incubadora</label>
                                            <input type="date" class="form-control @error('data_entrada_incubadora') is-invalid @enderror" id="data_entrada_incubadora" name="data_entrada_incubadora" value="{{ old('data_entrada_incubadora', $incubacao->data_entrada_incubadora->format('Y-m-d')) }}" required>
                                            @error('data_entrada_incubadora')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="quantidade_ovos">Quantidade de Ovos</label>
                                            <input type="number" class="form-control @error('quantidade_ovos') is-invalid @enderror" id="quantidade_ovos" name="quantidade_ovos" value="{{ old('quantidade_ovos', $incubacao->quantidade_ovos) }}" min="1" required>
                                            @error('quantidade_ovos')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="chocadeira">Chocadeira</label>
                                            <select class="form-control @error('chocadeira') is-invalid @enderror" id="chocadeira" name="chocadeira">
                                                <option value="">Selecione a Chocadeira</option>
                                                @foreach ($chocadeiras as $chocadeira)
                                                    <option value="{{ $chocadeira }}" {{ old('chocadeira', $incubacao->chocadeira) == $chocadeira ? 'selected' : '' }}>
                                                        {{ $chocadeira }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('chocadeira')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="postura_ovo_id">Postura de Ovo Associada (Opcional)</label>
                                            <select class="form-control @error('postura_ovo_id') is-invalid @enderror" id="postura_ovo_id" name="postura_ovo_id">
                                                <option value="">Nenhuma</option>
                                                @foreach ($posturasOvos as $posturaOvo)
                                                    <option value="{{ $posturaOvo->id }}" {{ old('postura_ovo_id', $incubacao->postura_ovo_id) == $posturaOvo->id ? 'selected' : '' }}>
                                                        ID: {{ $posturaOvo->id }} - Início: {{ \Carbon\Carbon::parse($posturaOvo->data_inicio_postura)->format('d/m/Y') }} - Qtd: {{ $posturaOvo->quantidade_ovos }}
                                                        @if ($posturaOvo->acasalamento && $posturaOvo->acasalamento->macho && $posturaOvo->acasalamento->femea)
                                                            - Macho: {{ $posturaOvo->acasalamento->macho->matricula }} / Fêmea: {{ $posturaOvo->acasalamento->femea->matricula }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('postura_ovo_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="quantidade_inferteis">Ovos Inférteis</label>
                                            <input type="number" class="form-control @error('quantidade_inferteis') is-invalid @enderror" id="quantidade_inferteis" name="quantidade_inferteis" value="{{ old('quantidade_inferteis', $incubacao->quantidade_inferteis) }}" min="0">
                                            @error('quantidade_inferteis')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="quantidade_infectados">Ovos Infectados</label>
                                            <input type="number" class="form-control @error('quantidade_infectados') is-invalid @enderror" id="quantidade_infectados" name="quantidade_infectados" value="{{ old('quantidade_infectados', $incubacao->quantidade_infectados) }}" min="0">
                                            @error('quantidade_infectados')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="quantidade_mortos">Ovos Mortos</label>
                                            <input type="number" class="form-control @error('quantidade_mortos') is-invalid @enderror" id="quantidade_mortos" name="quantidade_mortos" value="{{ old('quantidade_mortos', $incubacao->quantidade_mortos) }}" min="0">
                                            @error('quantidade_mortos')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="quantidade_eclodidos">Quantidade de Ovos Eclodidos</label>
                                        <input type="number" class="form-control @error('quantidade_eclodidos') is-invalid @enderror" id="quantidade_eclodidos" name="quantidade_eclodidos" value="{{ old('quantidade_eclodidos', $incubacao->quantidade_eclodidos) }}" min="0" max="{{ $incubacao->quantidade_ovos }}" required>
                                        @error('quantidade_eclodidos')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $incubacao->observacoes) }}</textarea>
                                        @error('observacoes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" value="1" {{ old('ativo', $incubacao->ativo) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="ativo">Incubação Ativa</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Atualizar</button>
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

    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
<!-- ./wrapper -->
