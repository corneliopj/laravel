<div class="card card-outline card-secondary mt-3 item-row" data-index="{{ $index }}">
    <div class="card-header">
        {{-- Correção: Força $index para inteiro antes da soma --}}
        <h3 class="card-title">Item #{{ (int)$index + 1 }}</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool remove-item" title="Remover Item">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="items_{{ $index }}_ave_id">Ave (Opcional)</label>
                <select class="form-control select2-ave @error('items.' . $index . '.ave_id') is-invalid @enderror"
                        id="item-ave-id-{{ $index }}"
                        name="items[{{ $index }}][ave_id]">
                    <option value="">Selecione uma ave</option>
                    @if (isset($item['ave_id']) && $item['ave_id'])
                        @php
                            // Garante que $avesDisponiveis é uma coleção para usar firstWhere
                            $avesDisponiveisCollection = collect($avesDisponiveis);
                            $selectedAve = $avesDisponiveisCollection->firstWhere('id', $item['ave_id']);
                        @endphp
                        @if ($selectedAve)
                            <option value="{{ $selectedAve->id }}" selected>
                                {{ $selectedAve->matricula }} ({{ $selectedAve->tipoAve->nome ?? 'N/A' }})
                            </option>
                        @else
                            {{-- Se a ave não estiver na lista de disponíveis (ex: já foi vendida ou inativada),
                                 ainda exibe a opção se ela estava associada a este item --}}
                            <option value="{{ $item['ave_id'] }}" selected>
                                Ave ID: {{ $item['ave_id'] }} (Não disponível para seleção)
                            </option>
                        @endif
                    @endif
                </select>
                @error('items.' . $index . '.ave_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="items_{{ $index }}_descricao_item">Descrição do Item <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('items.' . $index . '.descricao_item') is-invalid @enderror"
                       id="items_{{ $index }}_descricao_item"
                       name="items[{{ $index }}][descricao_item]"
                       value="{{ old('items.' . $index . '.descricao_item', $item['descricao_item'] ?? '') }}"
                       placeholder="Ex: Ave, Ovo, Serviço" required>
                @error('items.' . $index . '.descricao_item')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="items_{{ $index }}_quantidade">Quantidade <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('items.' . $index . '.quantidade') is-invalid @enderror"
                       id="items_{{ $index }}_quantidade"
                       name="items[{{ $index }}][quantidade]"
                       value="{{ old('items.' . $index . '.quantidade', $item['quantidade'] ?? 1) }}" min="1" required>
                @error('items.' . $index . '.quantidade')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="items_{{ $index }}_preco_unitario">Preço Unitário <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control @error('items.' . $index . '.preco_unitario') is-invalid @enderror"
                       id="items_{{ $index }}_preco_unitario"
                       name="items[{{ $index }}][preco_unitario]"
                       value="{{ old('items.' . $index . '.preco_unitario', $item['preco_unitario'] ?? 0.00) }}" min="0.01" required>
                @error('items.' . $index . '.preco_unitario')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label>Total do Item</label>
                <p class="form-control-static item-total-display">R$ {{ number_format(((float)($item['quantidade'] ?? 0)) * ((float)($item['preco_unitario'] ?? 0)), 2, ',', '.') }}</p>
                {{-- Correção: Garante que os valores são floats antes de salvar no hidden --}}
                <input type="hidden" name="items[{{ $index }}][valor_total_item]" value="{{ number_format(((float)($item['quantidade'] ?? 0)) * ((float)($item['preco_unitario'] ?? 0)), 2, '.', '') }}">
            </div>
        </div>
    </div>
</div>
