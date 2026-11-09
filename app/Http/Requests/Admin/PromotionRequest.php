<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_banner' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul promo wajib diisi.',
            'title.max' => 'Judul promo maksimal 255 karakter.',
            'starts_at.required' => 'Tanggal mulai promo wajib diisi.',
            'starts_at.date' => 'Tanggal mulai promo tidak valid.',
            'ends_at.required' => 'Tanggal selesai promo wajib diisi.',
            'ends_at.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'branch_id.exists' => 'Cabang tidak ditemukan.',
            'banner.image' => 'Banner harus berupa gambar.',
            'banner.mimes' => 'Banner harus JPG, PNG, atau WebP.',
            'banner.max' => 'Banner maksimal 2 MB.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function promotionData(): array
    {
        $data = $this->only(['title', 'description', 'starts_at', 'ends_at', 'terms', 'branch_id']);
        $data['branch_id'] = $data['branch_id'] ?? null;
        if ($data['branch_id'] === '') {
            $data['branch_id'] = null;
        }

        return $data;
    }
}
