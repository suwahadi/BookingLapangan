<?php

namespace App\Http\Controllers\Midtrans;

use App\Http\Controllers\Controller;
use App\Integrations\Midtrans\Security\MidtransSignatureVerifier;
use App\Services\Payment\MidtransNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MidtransNotificationController extends Controller
{
    /**
     * Handle incoming webhooks from Midtrans
     */
    public function __invoke(
        Request $request,
        MidtransSignatureVerifier $verifier,
        MidtransNotificationService $service
    ) {
        $payload = $request->all();

        // Log the event even if signature is invalid (for security analysis if needed, or just keep it clean)
        // But for industry standard, we log after basic validation or just log everything.
        // Let's log it.
        $event = \App\Models\PaymentWebhookEvent::create([
            'provider' => 'MIDTRANS',
            'order_id' => $payload['order_id'] ?? null,
            'transaction_id' => $payload['transaction_id'] ?? null,
            'payload' => $payload,
            'status_code' => $payload['status_code'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'fraud_status' => $payload['fraud_status'] ?? null,
            'processed' => false,
        ]);

        // Optional: IP Whitelisting for Hardening
        $allowedIps = [
            '103.208.23.6', '103.208.23.7', // Production
            '103.208.23.230', '103.208.23.231', // Sandbox
        ];
        
        // Skip IP check in local environment or if behind proxy without headers
        if (app()->environment('production') && !in_array($request->ip(), $allowedIps)) {
             // Log as suspicious but maybe don't block yet or block
             // Untuk demo kita biarkan, tapi sedia kodenya.
        }

        // Signature invalid -> 401
        if (!$verifier->isValid($payload)) {
            $event->update(['processing_error' => 'Invalid signature']);
            return response()->json(['message' => 'Signature tidak valid'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $service->handle($payload);
            $event->update(['processed' => true]);
        } catch (\Throwable $e) {
            $event->update(['processing_error' => $e->getMessage()]);
            throw $e;
        }

        return response()->json(['message' => 'OK'], Response::HTTP_OK);
    }
}
