<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObatUpdateRequest extends FormRequest
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
            'nama_obat' => ['required', 'string'],
            'harga_jual' => ['required', 'numeric'],
            'harga_beli' => ['required', 'numeric'],
            'stok' => ['required', 'numeric'],
            'status' => ['required', 'boolean'],
        ];
    }
}
