<?php

namespace App\Integrations\Midtrans;

use Illuminate\Support\Facades\Http;

class MidtransClient
{
    private string $serverKey;
    private string $baseUrl;
    private bool $isProduction;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->baseUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Create charge transaction
     * 
     * @param array $payload
     * @return array Response from Midtrans
     * @throws \Exception
     */
    public function charge(array $payload): array
    {
        $response = Http::withBasicAuth($this->serverKey, '')
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post("{$this->baseUrl}/charge", $payload);

        if ($response->failed()) {
            throw new \Exception('Midtrans charge failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get transaction status
     * 
     * @param string $orderId
     * @return array
     * @throws \Exception
     */
    public function getStatus(string $orderId): array
    {
        $response = Http::withBasicAuth($this->serverKey, '')
            ->get("{$this->baseUrl}/{$orderId}/status");

        if ($response->failed()) {
            throw new \Exception('Midtrans status check failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Cancel transaction
     * 
     * @param string $orderId
     * @return array
     * @throws \Exception
     */
    public function cancel(string $orderId): array
    {
        $response = Http::withBasicAuth($this->serverKey, '')
            ->post("{$this->baseUrl}/{$orderId}/cancel");

        if ($response->failed()) {
            throw new \Exception('Midtrans cancel failed: ' . $response->body());
        }

        return $response->json();
    }
}
