<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PendaftaranStoreRequest extends FormRequest
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
            'tglDaftar' => ['required', 'date'],
            'noAntrian' => ['required', 'string'],
            'keluhan' => ['nullable', 'string'],
            'kunjSakit' => ['nullable', 'string'],
            'sistole' => ['nullable', 'numeric'],
            'diastole' => ['nullable', 'numeric'],
            'beratBadan' => ['nullable', 'numeric'],
            'tinggiBadan' => ['nullable', 'numeric'],
            'respRate' => ['nullable', 'numeric'],
            'lingkarPerut' => ['nullable', 'numeric'],
            'heartRate' => ['nullable', 'numeric'],
            'rujukBalik' => ['nullable', 'integer'],
            'start_inProgress' => ['nullable'],
            'end_inProgress' => ['nullable'],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
            'deleted_at' => ['nullable'],
            'deleted_by' => ['nullable', 'integer'],
            'pasien_id' => ['required', 'integer', 'exists:pasiens,id'],
            'poli_id' => ['required', 'integer', 'exists:polis,id'],
            'practitioner_id' => ['required', 'integer', 'exists:practitioners,id'],
            'tkp_id' => ['required', 'integer', 'exists:tkps,id'],
        ];
    }
}
