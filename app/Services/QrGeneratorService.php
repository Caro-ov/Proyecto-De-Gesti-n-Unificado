<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QrGeneratorService
{
    public function generateBase64(string $token): string
    {
        try {
            $url = $this->getQrImageUrl($token);

            // Usar HTTP client en lugar de file_get_contents
            $response = Http::timeout(10)
                ->get($url);

            if ($response->successful()) {
                return base64_encode($response->body());
            }

            // Si falla, retornar un placeholder base64
            return $this->getPlaceholderBase64();
        } catch (\Exception $e) {
            // Loguear error pero no fallar el job
            \Illuminate\Support\Facades\Log::error('QR generation failed: ' . $e->getMessage());

            return $this->getPlaceholderBase64();
        }
    }

    public function generateSvg(string $token): string
    {
        // Retornar URL del QR para web
        return $this->getQrImageUrl($token);
    }

    private function getQrImageUrl(string $token): string
    {
        // API pública gratuita - genera QR al vuelo
        return sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=%s',
            urlencode($token)
        );
    }

    /**
     * Retorna una imagen placeholder si falla la generación
     * Base64 de un pixel transparente 1x1
     */
    private function getPlaceholderBase64(): string
    {
        return 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    }
}
