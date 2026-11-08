<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BranchRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'opening_time' => ['required', 'date_format:H:i'],
            'closing_time' => ['required', 'date_format:H:i'],
            'admin_whatsapp' => ['required', 'string', 'max:20'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
            'pic_email' => ['nullable', 'email', 'max:255'],
            'google_maps_link' => ['nullable', 'url', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $opening = $this->input('opening_time');
            $closing = $this->input('closing_time');

            if (! is_string($opening) || ! is_string($closing)) {
                return;
            }

            // Jam tutup 00:00 berarti tengah malam (24:00), bukan awal hari.
            $openingMinutes = $this->toMinutes($opening);
            $closingMinutes = $closing === '00:00' ? 24 * 60 : $this->toMinutes($closing);

            if ($openingMinutes >= $closingMinutes) {
                $validator->errors()->add('closing_time', 'Jam tutup harus setelah jam buka.');
            }
        });
    }

    private function toMinutes(string $time): int
    {
        [$hour, $minute] = explode(':', $time);

        return ((int) $hour * 60) + (int) $minute;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama cabang wajib diisi.',
            'name.max' => 'Nama cabang maksimal 255 karakter.',
            'address.required' => 'Alamat cabang wajib diisi.',
            'address.max' => 'Alamat cabang maksimal 500 karakter.',
            'opening_time.required' => 'Jam buka wajib diisi.',
            'opening_time.date_format' => 'Jam buka harus dalam format jam:menit, contoh 07:00.',
            'closing_time.required' => 'Jam tutup wajib diisi.',
            'closing_time.date_format' => 'Jam tutup harus dalam format jam:menit, contoh 22:00.',
            'admin_whatsapp.required' => 'Nomor WhatsApp admin wajib diisi.',
            'admin_whatsapp.max' => 'Nomor WhatsApp admin maksimal 20 karakter.',
            'telegram_chat_id.max' => 'ID chat Telegram maksimal 255 karakter.',
            'pic_email.email' => 'Email penanggung jawab harus berupa alamat email yang valid.',
            'pic_email.max' => 'Email penanggung jawab maksimal 255 karakter.',
            'google_maps_link.url' => 'Tautan Google Maps harus berupa URL yang valid.',
            'google_maps_link.max' => 'Tautan Google Maps maksimal 1000 karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama cabang',
            'address' => 'alamat',
            'opening_time' => 'jam buka',
            'closing_time' => 'jam tutup',
            'admin_whatsapp' => 'nomor WhatsApp admin',
            'telegram_chat_id' => 'ID chat Telegram',
            'pic_email' => 'email penanggung jawab',
            'google_maps_link' => 'tautan Google Maps',
        ];
    }
}
