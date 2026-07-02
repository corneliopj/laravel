<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'matricula' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aves', 'matricula')->ignore($this->route('ave')->id),
            ],
            'tipo_ave_id' => 'required|exists:tipos_aves,id',
            'variacao_id' => 'required|exists:variacoes,id',
            'sexo' => ['required', Rule::in(['Macho', 'Femea', 'Indefinido'])],
            'data_eclosao' => 'required|date|before_or_equal:today',
            'lote_id' => 'nullable|exists:lotes,id',
            'incubacao_id' => 'nullable|exists:incubacoes,id',
            'pai_id' => 'nullable|exists:aves,id',
            'mae_id' => 'nullable|exists:aves,id',
            'criatorio_origem' => 'nullable|string|max:20',
            'registro_abrasb' => 'nullable|string|max:15',
            'peso_nascimento' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'remover_foto_atual' => 'nullable|boolean',
        ];
    }
}
