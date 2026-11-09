<?php

namespace App\Http\Requests\Admin;

use App\Models\CapacitySlot;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CapacitySlotRequest extends FormRequest
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
     * Kolom kuota mengikuti skema resmi (1.1): max_quota, plus unique
     * kombinasi cabang + tanggal + jam mulai + jam selesai.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'branch_id' => ['required', 'exists:branches,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i', 'before:end_time'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'max_quota' => ['required', 'integer', 'min:1'],
            'estimated_walk_in' => ['sometimes', 'integer', 'min:0'],
        ];

        if ($this->filled(['branch_id', 'date', 'start_time', 'end_time'])) {
            $slot = $this->route('slot');
            $ignoreId = $slot instanceof CapacitySlot ? $slot->id : null;

            $rules['branch_id'][] = Rule::unique('capacity_slots', 'branch_id')
                ->where('date', $this->input('date'))
                ->where('start_time', $this->input('start_time'))
                ->where('end_time', $this->input('end_time'))
                ->ignore($ignoreId);
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'branch_id.required' => 'Cabang wajib dipilih.',
            'branch_id.exists' => 'Cabang yang dipilih tidak ditemukan.',
            'branch_id.unique' => 'Kuota untuk cabang, tanggal, dan jam ini sudah ada.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal harus berupa tanggal yang valid.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'start_time.date_format' => 'Jam mulai harus dalam format jam:menit, contoh 09:00.',
            'start_time.before' => 'Jam mulai harus sebelum jam selesai.',
            'end_time.required' => 'Jam selesai wajib diisi.',
            'end_time.date_format' => 'Jam selesai harus dalam format jam:menit, contoh 12:00.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'max_quota.required' => 'Kapasitas maksimal wajib diisi.',
            'max_quota.integer' => 'Kapasitas maksimal harus berupa angka.',
            'max_quota.min' => 'Kapasitas maksimal minimal 1.',
            'estimated_walk_in.integer' => 'Estimasi walk-in harus berupa angka.',
            'estimated_walk_in.min' => 'Estimasi walk-in tidak boleh kurang dari 0.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'branch_id' => 'cabang',
            'date' => 'tanggal',
            'start_time' => 'jam mulai',
            'end_time' => 'jam selesai',
            'max_quota' => 'kapasitas maksimal',
            'estimated_walk_in' => 'estimasi walk-in',
        ];
    }
}
