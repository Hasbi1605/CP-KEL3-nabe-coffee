<?php

namespace App\Http\Requests\Admin;

use App\Models\Menu;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(Menu::CATEGORIES)],
            'price' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'tags' => ['nullable', 'string', 'max:500'],
            'is_best_seller' => ['sometimes', 'boolean'],
            'is_available' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'branch_id.required' => 'Cabang wajib dipilih.',
            'branch_id.exists' => 'Cabang yang dipilih tidak ditemukan.',
            'name.required' => 'Nama menu wajib diisi.',
            'name.max' => 'Nama menu maksimal 255 karakter.',
            'category.required' => 'Kategori menu wajib dipilih.',
            'category.in' => 'Kategori menu harus salah satu dari: kopi, non-kopi, makanan berat, atau camilan.',
            'price.required' => 'Harga menu wajib diisi.',
            'price.integer' => 'Harga menu harus berupa angka.',
            'price.min' => 'Harga menu tidak boleh kurang dari 0.',
            'photo.image' => 'Foto menu harus berupa gambar.',
            'photo.max' => 'Ukuran foto menu maksimal 2 MB.',
            'tags.max' => 'Tag menu maksimal 500 karakter.',
            'is_best_seller.boolean' => 'Penanda terlaris harus berupa ya atau tidak.',
            'is_available.boolean' => 'Penanda ketersediaan harus berupa ya atau tidak.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'branch_id' => 'cabang',
            'name' => 'nama menu',
            'category' => 'kategori',
            'price' => 'harga',
            'photo' => 'foto menu',
            'tags' => 'tag',
            'is_best_seller' => 'terlaris',
            'is_available' => 'ketersediaan',
        ];
    }
}
