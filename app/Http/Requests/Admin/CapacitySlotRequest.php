<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i', 'before:end_time'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'max_capacity' => ['required', 'integer', 'min:1'],
            'estimated_walk_in' => ['sometimes', 'integer', 'min:0'],
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
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal harus berupa tanggal yang valid.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'start_time.date_format' => 'Jam mulai harus dalam format jam:menit, contoh 09:00.',
            'start_time.before' => 'Jam mulai harus sebelum jam selesai.',
            'end_time.required' => 'Jam selesai wajib diisi.',
            'end_time.date_format' => 'Jam selesai harus dalam format jam:menit, contoh 12:00.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'max_capacity.required' => 'Kapasitas maksimal wajib diisi.',
            'max_capacity.integer' => 'Kapasitas maksimal harus berupa angka.',
            'max_capacity.min' => 'Kapasitas maksimal minimal 1.',
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
            'max_capacity' => 'kapasitas maksimal',
            'estimated_walk_in' => 'estimasi walk-in',
        ];
    }
}
