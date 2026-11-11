<?php

namespace App\Http\Requests\Menu;

use App\Enums\MenuCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', Rule::in([...array_keys(MenuCategory::options()), 'all'])],
            'max_price' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.in' => 'Kategori menu tidak valid.',
            'max_price.integer' => 'Harga maksimal harus angka.',
            'branch_id.exists' => 'Cabang tidak ditemukan.',
            'search.max' => 'Kata kunci maksimal 100 karakter.',
        ];
    }

    /**
     * @return array{category: ?string, max_price: ?int, branch_id: ?int, search: ?string}
     */
    public function filters(): array
    {
        $category = $this->input('category');
        $category = ($category === null || $category === '' || $category === 'all') ? null : $category;

        return [
            'category' => $category,
            'max_price' => $this->input('max_price') !== null ? (int) $this->input('max_price') : null,
            'branch_id' => $this->input('branch_id') !== null ? (int) $this->input('branch_id') : null,
            'search' => $this->input('search'),
        ];
    }
}
