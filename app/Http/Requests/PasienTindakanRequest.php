<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasienTindakanRequest extends FormRequest
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
            'tindakan_id' => ['required', 'integer', 'exists:tindakans,id'],
            'practitioner_id' => ['required', 'integer', 'exists:practitioners,id'],
            'biaya' => ['required', 'integer'],
        ];
    }
}
