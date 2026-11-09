<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomepageContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'content' => ['nullable', 'array'],
            'content.outlet_info_title' => ['nullable', 'string', 'max:255'],
            'content.outlet_info_text' => ['nullable', 'string', 'max:2000'],
            'content.promo_title' => ['nullable', 'string', 'max:255'],
            'content.promo_text' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.outlet_info_title.max' => 'Judul info outlet maksimal 255 karakter.',
            'content.outlet_info_text.max' => 'Teks info outlet maksimal 2000 karakter.',
            'content.promo_title.max' => 'Judul promo maksimal 255 karakter.',
            'content.promo_text.max' => 'Teks promo maksimal 2000 karakter.',
        ];
    }
}
