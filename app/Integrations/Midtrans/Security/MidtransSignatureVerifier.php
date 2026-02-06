<?php

namespace App\Integrations\Midtrans\Security;

class MidtransSignatureVerifier
{
    /**
     * Verify Midtrans notification signature
     * Formula: sha512(order_id + status_code + gross_amount + server_key)
     */
    public function isValid(array $payload): bool
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            return false;
        }

        $serverKey = (string) config('midtrans.server_key');

        $raw = $orderId . $statusCode . $grossAmount . $serverKey;
        $expected = hash('sha512', $raw);

        return hash_equals($expected, $signatureKey);
    }
}
