<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePlanningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'request_code' => ['required', 'string', 'max:100'],
            'candidate_token' => ['required', 'string', 'max:100'],
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.slot_order' => ['required', 'integer', 'min:0', 'distinct'],
            'slots.*.slot_name' => ['required', 'string', 'max:50'],
            'slots.*.original_quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'slots.required' => 'Rencana untuk seluruh slot wajib diisi.',
            'slots.min' => 'Minimal harus ada 1 slot dalam rencana.',
            'slots.*.slot_order.distinct' => 'Terdapat slot_order duplikat dalam satu request.',
            'slots.*.original_quantity.integer' => 'Nilai slot :position harus berupa bilangan bulat (pecahan tidak valid).',
            'slots.*.original_quantity.min' => 'Nilai slot :position tidak boleh negatif.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validasi gagal.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
