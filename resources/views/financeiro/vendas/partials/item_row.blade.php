{{-- resources/views/vendas/partials/item_row.blade.php --}}
<div class="card card-outline card-secondary item-row mb-3">
    <div class="card-header">
        <h3 class="card-title">Item #{{ $index + 1 }}</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool remove-item-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>Tipo de Item</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="items[{{ $index }}][tipo_item]" id="tipo_individual_{{ $index }}" value="individual" {{ (old('items.' . $index . '.tipo_item', $item->ave_id ? 'individual' : ($item->plantel_id ? 'plantel' : 'generico'))) == 'individual' ? 'checked' : '' }}>
                <label class="form-check-label" for="tipo_individual_{{ $index }}">Ave Individual</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="items[{{ $index }}][tipo_item]" id="tipo_plantel_{{ $index }}" value="plantel" {{ (old('items.' . $index . '.tipo_item', $item->ave_id ? 'individual' : ($item->plantel_id ? 'plantel' : 'generico'))) == 'plantel' ? 'checked' : '' }}>
                <label class="form-check-label" for="tipo_plantel_{{ $index }}">Plantel Agrupado</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="items[{{ $index }}][tipo_item]" id="tipo_generico_{{ $index }}" value="generico" {{ (old('items.' . $index . '.tipo_item', $item->ave_id ? 'individual' : ($item->plantel_id ? 'plantel' : 'generico'))) == 'generico' ? 'checked' : '' }}>
                <label class="form-check-label" for="tipo_generico_{{ $index }}">Genérico</label>
            </div>
            @error('items.' . $index . '.tipo_item')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div id="div_ave_id_{{ $index }}" class="form-group" style="{{ (old('items.' . $index . '.tipo_item', $item->ave_id ? 'individual' : ($item->plantel_id ? 'plantel' : 'generico'))) == 'individual' ? '' : 'display:none;' }}">
            <label for="items_{{ $index }}_ave_id">Ave Individual</label>
            <select name="items[{{ $index }}][ave_id]" id="items_{{ $index }}_ave_id" class="form-control @error('items.' . $index . '.ave_id') is-invalid @enderror">
                <option value="">Selecione uma Ave</option>
                @foreach($avesDisponiveis as $ave)
                    <option value="{{ $ave->id }}" {{ old('items.' . $index . '.ave_id', $item->ave_id ?? '') == $ave->id ? 'selected' : '' }}>{{ $ave->matricula }} ({{ $ave->tipoAve->nome ?? 'N/A' }})</option>
                @endforeach
            </select>
            @error('items.' . $index . '.ave_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div id="div_plantel_id_{{ $index }}" style="{{ (old('items.' . $index . '.tipo_item', $item->ave_id ? 'individual' : ($item->plantel_id ? 'plantel' : 'generico'))) == 'plantel' ? '' : 'display:none;' }}">
            <div class="form-group">
                <label for="items_{{ $index }}_plantel_id">Plantel Agrupado</label>
                <select name="items[{{ $index }}][plantel_id]" id="items_{{ $index }}_plantel_id" class="form-control @error('items.' . $index . '.plantel_id') is-invalid @enderror">
                    <option value="">Selecione um Plantel</option>
                    @foreach($plantelOptions as $plantel)
                        <option value="{{ $plantel->id }}" {{ old('items.' . $index . '.plantel_id', $item->plantel_id ?? '') == $plantel->id ? 'selected' : '' }}>{{ $plantel->identificacao_grupo }} (Qtd. Atual: {{ $plantel->quantidade_atual }})</option>
                    @endforeach
                </select>
                @error('items.' . $index . '.plantel_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="items_{{ $index }}_descricao_item">Descrição do Item</label>
            <input type="text" name="items[{{ $index }}][descricao_item]" id="items_{{ $index }}_descricao_item" class="form-control @error('items.' . $index . '.descricao_item') is-invalid @enderror" value="{{ old('items.' . $index . '.descricao_item', $item->descricao_item ?? '') }}" placeholder="Ex: Ave, Ovo, Raçao, Codornas" required>
            @error('items.' . $index . '.descricao_item')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="items_{{ $index }}_quantidade">Quantidade</label>
            <input type="number" name="items[{{ $index }}][quantidade]" id="items_{{ $index }}_quantidade" class="form-control @error('items.' . $index . '.quantidade') is-invalid @enderror" value="{{ old('items.' . $index . '.quantidade', $item->quantidade ?? 1) }}" min="1" required {{ (old('items.' . $index . '.tipo_item', $item->ave_id ? 'individual' : ($item->plantel_id ? 'plantel' : 'generico'))) == 'individual' ? 'readonly' : '' }}>
            @error('items.' . $index . '.quantidade')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="items_{{ $index }}_preco_unitario">Preço Unitário (R$)</label>
            <input type="number" name="items[{{ $index }}][preco_unitario]" id="items_{{ $index }}_preco_unitario" class="form-control @error('items.' . $index . '.preco_unitario') is-invalid @enderror" value="{{ old('items.' . $index . '.preco_unitario', $item->preco_unitario ?? '') }}" step="0.01" min="0.01" required>
            @error('items.' . $index . '.preco_unitario')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
