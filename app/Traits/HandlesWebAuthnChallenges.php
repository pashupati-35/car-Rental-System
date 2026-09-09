<?php

namespace App\Traits;

trait HandlesWebAuthnChallenges
{
    protected function rpId(): string
    {
        return parse_url(config('app.url'), PHP_URL_HOST) ?: request()->getHost();
    }

    protected function generateChallenge(int $length = 32): string
    {
        return $this->base64urlEncode(random_bytes($length));
    }

    protected function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64urlDecode(string $data): string|false
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }

    protected function decodeClientData(string $clientDataB64): ?array
    {
        try {
            $decoded = $this->base64urlDecode($clientDataB64);
            if ($decoded === false) {
                return null;
            }

            $json = json_decode($decoded, true);

            return is_array($json) ? $json : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
