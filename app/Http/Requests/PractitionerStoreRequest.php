<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PractitionerStoreRequest extends FormRequest
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
            'namaPractitioner' => ['required', 'string'],
            'nikPractitioner' => ['required', 'string'],
            'practitioner_group_id' => ['required', 'integer', 'exists:practitioner_groups,id'],
        ];
    }
}
