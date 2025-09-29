<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasienPemeriksaanRequest extends FormRequest
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
            'practitioner_id' => ['required', 'integer', 'exists:practitioners,id'],
            'kategori_pemeriksaan_id' => ['required', 'integer', 'exists:kategori_pemeriksaans,id'],
            'hasil_pemeriksaan' => ['required', 'string'],
            'biaya' => ['required', 'integer'],
        ];
    }
}
