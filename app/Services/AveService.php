<?php

namespace App\Services;

use App\Models\Ave;
use App\Models\Morte;
use App\Models\TipoAve;
use App\Models\Variacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class AveService
{
    /**
     * Recupera aves paginadas com filtros aplicados.
     */
    public function paginateAves(Request $request)
    {
        $query = Ave::query()->select(['id', 'matricula', 'tipo_ave_id', 'variacao_id', 'lote_id', 'data_eclosao', 'sexo', 'ativo', 'deleted_at', 'foto_path']);
        $query->with([
            'tipoAve:id,nome',
            'variacao:id,nome',
            'lote:id,identificacao_lote',
            'mortes:id,ave_id'
        ]);

        $status = $request->input('status');

        switch ($status) {
            case 'ativas':
                $query->where('ativo', true)
                      ->doesntHave('mortes')
                      ->whereNull('deleted_at');
                break;
            case 'excluidas':
                $query->onlyTrashed();
                break;
            case 'mortas':
                $query->whereHas('mortes')
                      ->withTrashed();
                break;
            case 'inativas':
                $query->where('ativo', false)
                      ->doesntHave('mortes')
                      ->whereNull('deleted_at');
                break;
            default:
                $query->withTrashed();
                break;
        }

        if ($request->filled('tipo_ave_id')) {
            $query->where('tipo_ave_id', $request->tipo_ave_id);
        }

        if ($request->filled('variacao_id')) {
            $query->where('variacao_id', $request->variacao_id);
        }

        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('matricula', 'like', "%{$searchTerm}%");
        }

        $sortColumn = $request->get('sort', 'matricula');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortColumn, $sortDirection);

        return $query->paginate(10);
    }

    /**
     * Cria uma nova ave.
     */
    public function storeAve(array $data, $foto = null)
    {
        $data['ativo'] = true;

        if ($foto) {
            $imagePath = $foto->store('uploads/aves', 'public');
            $data['foto_path'] = $imagePath;
        } else {
            $data['foto_path'] = null;
        }

        return Ave::create($data);
    }

    /**
     * Atualiza os dados de uma ave.
     */
    public function updateAve(Ave $ave, array $data, $foto = null, $removerFotoAtual = false)
    {
        if ($removerFotoAtual && $ave->foto_path && Storage::disk('public')->exists($ave->foto_path)) {
            Storage::disk('public')->delete($ave->foto_path);
            $data['foto_path'] = null;
        }

        if ($foto) {
            if ($ave->foto_path && Storage::disk('public')->exists($ave->foto_path)) {
                Storage::disk('public')->delete($ave->foto_path);
            }
            $imagePath = $foto->store('uploads/aves', 'public');
            $data['foto_path'] = $imagePath;
        }

        $ave->update($data);
        return $ave;
    }

    /**
     * Inativa e deleta (soft delete) uma ave.
     */
    public function deleteAve(Ave $ave)
    {
        $ave->ativo = false;
        $ave->save();
        return $ave->delete();
    }

    /**
     * Restaura uma ave deletada.
     */
    public function restoreAve($id)
    {
        $ave = Ave::withTrashed()->findOrFail($id);
        $ave->restore();
        $ave->ativo = true;
        $ave->save();
        return $ave;
    }

    /**
     * Exclui permanentemente uma ave e seu arquivo de foto.
     */
    public function forceDeleteAve($id)
    {
        $ave = Ave::withTrashed()->findOrFail($id);

        if ($ave->foto_path && Storage::disk('public')->exists($ave->foto_path)) {
            Storage::disk('public')->delete($ave->foto_path);
        }

        return $ave->forceDelete();
    }

    /**
     * Registra a morte de uma ave.
     */
    public function registerDeath(Ave $ave, array $data)
    {
        if ($ave->mortes()->exists()) {
            throw new \Exception('Esta ave já possui um registro de morte.');
        }

        Morte::create([
            'ave_id' => $ave->id,
            'data_morte' => $data['data_morte'],
            'causa' => $data['causa'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
        ]);

        $ave->ativo = false;
        $ave->save();

        return $ave;
    }

    /**
     * Gera e associa um código de validação único à certidão da ave.
     * Retorna o código gerado ou null se falhar.
     */
    public function expedirCertidao(Ave $ave)
    {
        $maxRetries = 10;
        $attempt = 0;
        $saved = false;
        $validationCode = null;

        while (!$saved && $attempt < $maxRetries) {
            try {
                do {
                    $hexPart = bin2hex(random_bytes(5));
                    $code = strtoupper(substr($hexPart, 0, 5) . '-' . substr($hexPart, 5, 5));
                } while (Ave::where('codigo_validacao_certidao', $code)->exists() && $attempt < $maxRetries);

                if ($attempt >= $maxRetries) {
                    break;
                }

                $validationCode = $code;
                $ave->codigo_validacao_certidao = $validationCode;
                $ave->save();

                $saved = true;
            } catch (QueryException $e) {
                if ($e->getCode() === '23000' || Str::contains($e->getMessage(), 'Integrity constraint violation')) {
                    $attempt++;
                    Log::warning("Colisão de código de validação detectada para ave ID {$ave->id}. Tentando novamente. Tentativa: {$attempt}");
                } else {
                    throw $e;
                }
            }
        }

        return $saved ? $validationCode : null;
    }

    /**
     * Valida uma certidão com base na matrícula e código de validação.
     */
    public function validateCertidao(string $matricula, string $codigoValidacao)
    {
        $codigoValidacao = strtoupper($codigoValidacao);

        return Ave::withTrashed()
                    ->where('matricula', $matricula)
                    ->where('codigo_validacao_certidao', $codigoValidacao)
                    ->first();
    }

    /**
     * Fornece sugestões de pesquisa para aves.
     */
    public function searchSuggestions(string $query)
    {
        if (empty($query)) {
            return [];
        }

        $aves = Ave::with('tipoAve')
                    ->where('matricula', 'like', '%' . $query . '%')
                    ->orWhereHas('tipoAve', function ($q) use ($query) {
                        $q->where('nome', 'like', '%' . $query . '%');
                    })
                    ->limit(10)
                    ->get();

        return $aves->map(function($ave) {
            $tipoAveNome = $ave->tipoAve->nome ?? 'Tipo Desconhecido';
            return [
                'id' => $ave->id,
                'matricula' => $ave->matricula,
                'tipo_ave_nome' => $tipoAveNome,
                'text' => "{$ave->matricula} ({$tipoAveNome})",
            ];
        });
    }

    /**
     * Realiza a busca completa de aves.
     */
    public function searchAves(string $query)
    {
        if (empty($query)) {
            return null;
        }

        return Ave::where('matricula', 'like', '%' . $query . '%')
                       ->orWhereHas('tipoAve', function ($q) use ($query) {
                           $q->where('nome', 'like', '%' . $query . '%');
                       })
                       ->orWhereHas('variacao', function ($q) use ($query) {
                           $q->where('nome', 'like', '%' . $query . '%');
                       })
                       ->with(['tipoAve', 'variacao', 'lote'])
                       ->paginate(10);
    }
}
