<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BundleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust this if needed based on authentication or authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'uuidEnconter' => 'required|uuid',
            'Org_id' => 'required|string',
            'Patient_id' => 'required|string',
            'Patient_Name' => 'required|string',
            'Practitioner_id' => 'required|string',
            'Practitioner_Name' => 'required|string',
            'uuidEncounter' => 'required|uuid',
            'uuidCondition1' => 'required|uuid',
            'uuidCondition2' => 'required|uuid',
        ];
    }

    /**
     * Customize the error messages for validation.
     */
    public function messages(): array
    {
        return [
            'uuidEnconter.required' => 'The encounter UUID is required.',
            'uuidEnconter.uuid' => 'The encounter UUID must be a valid UUID.',
            'Org_id.required' => 'The organization ID is required.',
            'Patient_id.required' => 'The patient ID is required.',
            'Patient_Name.required' => 'The patient name is required.',
            'Practitioner_id.required' => 'The practitioner ID is required.',
            'Practitioner_Name.required' => 'The practitioner name is required.',
            'uuidCondition1.required' => 'The first condition UUID is required.',
            'uuidCondition1.uuid' => 'The first condition UUID must be a valid UUID.',
            'uuidCondition2.required' => 'The second condition UUID is required.',
            'uuidCondition2.uuid' => 'The second condition UUID must be a valid UUID.',
            'uuidEncounter.required' => 'The encounter UUID is required.',
        ];
    }
}
