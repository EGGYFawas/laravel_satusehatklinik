<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PemeriksaanStoreRequest extends FormRequest
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
            'id' => ['nullable', 'integer'],
            'keluhan' => ['nullable', 'string'],
            'kunjSakit' => ['nullable', 'string'],
            'suhu' => ['nullable', 'numeric'],
            'sistole' => ['nullable', 'numeric'],
            'diastole' => ['nullable', 'numeric'],
            'beratBadan' => ['nullable', 'numeric'],
            'tinggiBadan' => ['nullable', 'numeric'],
            'respRate' => ['nullable', 'numeric'],
            'lingkarPerut' => ['nullable', 'numeric'],
            'heartRate' => ['nullable', 'numeric'],
            'start_inProgress' => ['nullable'],
            'end_inProgress' => ['nullable'],
        ];
    }
}
