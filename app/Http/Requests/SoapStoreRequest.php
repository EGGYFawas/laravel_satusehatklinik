<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SoapStoreRequest extends FormRequest
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
            'subyektif' => ['required', 'string'],
            'assesment' => ['required', 'string'],
            'instruksi' => ['required', 'string'],
            'obyektif' => ['required', 'string'],
            'plan' => ['required', 'string'],
            'evaluasi' => ['required', 'string'],
            'end_inProgress' => ['nullable'],
        ];
    }
}
