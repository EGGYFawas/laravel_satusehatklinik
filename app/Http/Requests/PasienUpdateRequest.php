<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasienUpdateRequest extends FormRequest
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
            'nomorRm' => ['required', 'integer'],
            'noKartu' => ['required', 'string'],
            'nama' => ['required', 'string'],
            'sex' => ['required', 'string', 'max:1'],
            'tglLahir' => ['required', 'date'],
            'jnsPeserta' => ['required', 'integer'],
            'golDarah' => ['required', 'string'],
            'noHp' => ['required', 'string'],
            'noKtp' => ['required', 'string'],
            'pstProl' => ['required', 'string'],
            'pstPrb' => ['required', 'string'],
            'aktif' => ['required'],
            'ketAktif' => ['required', 'string'],
            'alamat' => ['required', 'string'],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
            'deleted_at' => ['nullable'],
            'deleted_by' => ['nullable', 'integer'],
            'KD_KELURAHAN' => ['nullable'],
        ];
    }
}
