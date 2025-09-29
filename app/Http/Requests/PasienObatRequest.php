<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasienObatRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'kunjungan_id' => ['required', 'integer', 'exists:pendaftarans,id'],
            'obat_id' => ['required', 'integer', 'exists:obats,id'],
            'jumlah_obat' => ['required', 'integer'],
            'harga_obat' => ['required', 'integer'],
            'instruksi' => ['nullable', 'string'],
        ];
    }
}
