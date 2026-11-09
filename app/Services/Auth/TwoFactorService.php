<?php

namespace App\Services\Auth;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class TwoFactorService
{
    public const RECOVERY_CODE_COUNT = 8;

    /**
     * @return array{secret: string, recovery_codes: list<string>}
     */
    public function generateSetup(): array
    {
        return [
            'secret' => Google2FA::generateSecretKey(),
            'recovery_codes' => Collection::times(self::RECOVERY_CODE_COUNT, fn () => Str::upper(Str::random(4)).'-'.Str::upper(Str::random(4)))
                ->all(),
        ];
    }

    public function qrCodeSvg(User $user, string $secret): string
    {
        $url = Google2FA::getQRCodeUrl(config('app.name', 'Nabe Coffee'), $user->email, $secret);
        $renderer = new ImageRenderer(new RendererStyle(192, 0), new SvgImageBackEnd);

        return (new Writer($renderer))->writeString($url);
    }

    public function verifyTotp(?string $secret, string $code): bool
    {
        if ($secret === null || $secret === '' || ! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        return Google2FA::verifyKey($secret, $code);
    }

    public function getCurrentTotp(?string $secret): ?string
    {
        if ($secret === null || $secret === '') {
            return null;
        }

        return Google2FA::getCurrentOtp($secret);
    }

    /**
     * Pakai satu recovery code; kode yang sudah dipakai dibuang.
     */
    public function consumeRecoveryCode(User $user, string $code): bool
    {
        $codes = collect($user->two_factor_recovery_codes ?? []);
        $normalized = Str::upper(trim($code));
        $index = $codes->search(fn (string $saved) => hash_equals(Str::upper($saved), $normalized));

        if ($index === false) {
            return false;
        }

        $codes->forget($index);
        $user->forceFill(['two_factor_recovery_codes' => $codes->values()->all()])->save();

        return true;
    }

    public function resetForUser(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    public function hasConfirmedTwoFactor(User $user): bool
    {
        return ! empty($user->two_factor_secret) && $user->two_factor_confirmed_at !== null;
    }
}
