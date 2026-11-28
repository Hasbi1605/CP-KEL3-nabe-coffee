<?php

namespace App\Http\Requests\Chatbot;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:1', 'max:500'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required' => 'Tulis pertanyaan terlebih dahulu sebelum mengirim.',
            'message.string' => 'Format pesan harus berupa teks.',
            'message.min' => 'Tulis pertanyaan terlebih dahulu sebelum mengirim.',
            'message.max' => 'Pertanyaan maksimal 500 karakter.',
            'branch_id.integer' => 'Pilihan cabang tidak valid.',
            'branch_id.exists' => 'Cabang yang dipilih tidak ditemukan.',
        ];
    }
}
