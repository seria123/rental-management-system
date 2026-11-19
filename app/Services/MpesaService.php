<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class MpesaService
{
    protected string $baseUrl;

    public function __construct()
    {
        $environment = $this->getSetting('environment', 'sandbox');

        $this->baseUrl = $environment === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    protected function getSetting(string $key, mixed $default = null): mixed
    {
        $value = config('services.mpesa.'.$key);

        if ($value !== null && $value !== '') {
            return $value;
        }

        return match ($key) {
            'consumer_key' => SystemSetting::get('mpesa_consumer_key', $default),
            'consumer_secret' => SystemSetting::get('mpesa_consumer_secret', $default),
            'passkey' => SystemSetting::get('mpesa_passkey', $default),
            'shortcode' => SystemSetting::get('mpesa_paybill', $default),
            'callback_url' => SystemSetting::get('mpesa_callback_url', $default),
            default => $default,
        };
    }

    public function hasCredentials(): bool
    {
        return ! empty($this->getSetting('consumer_key'))
            && ! empty($this->getSetting('consumer_secret'))
            && ! empty($this->getSetting('shortcode'))
            && ! empty($this->getSetting('passkey'));
    }

    /**
     * Generate M-Pesa access token.
     */
    public function getAccessToken(): string
    {
        $key = $this->getSetting('consumer_key');
        $secret = $this->getSetting('consumer_secret');

        if (! $key || ! $secret) {
            throw new RuntimeException('M-Pesa credentials are missing.');
        }

        $response = Http::withBasicAuth($key, $secret)
            ->acceptJson()
            ->get($this->baseUrl.'/oauth/v1/generate?grant_type=client_credentials');

        if (! $response->successful()) {
            Log::error('M-Pesa access token error', [
                'response' => $response->json(),
            ]);

            throw new RuntimeException('Unable to generate M-Pesa access token.');
        }

        return $response->json('access_token');
    }

    /**
     * Generate STK Push password.
     */
    protected function generatePassword(string $timestamp): string
    {
        return base64_encode(
            (string) $this->getSetting('shortcode').
            (string) $this->getSetting('passkey').
            $timestamp
        );
    }

    /**
     * Initiate STK Push.
     */
    public function stkPush(
        string $phone,
        float $amount,
        string $accountReference,
        string $description = 'Payment'
    ): array {

        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254'.substr($phone, 1);
        } elseif (str_starts_with($phone, '+254')) {
            $phone = substr($phone, 1);
        }

        if (! preg_match('/^254[17]\d{8}$/', $phone)) {
            throw new RuntimeException('Invalid Kenyan M-Pesa phone number.');
        }

        if ($amount < 1) {
            throw new RuntimeException('Amount must be at least KSh 1.');
        }

        $timestamp = now()->format('YmdHis');

        $payload = [
            'BusinessShortCode' => $this->getSetting('shortcode'),
            'Password' => $this->generatePassword($timestamp),
            'Timestamp' => $timestamp,
            'TransactionType' => config('services.mpesa.transaction_type', 'CustomerPayBillOnline') ?: 'CustomerPayBillOnline',
            'Amount' => (int) ceil($amount),
            'PartyA' => $phone,
            'PartyB' => $this->getSetting('shortcode'),
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->getSetting('callback_url'),
            'AccountReference' => Str::limit($accountReference, 12, ''),
            'TransactionDesc' => Str::limit($description, 13, ''),
        ];

        $response = Http::withToken($this->getAccessToken())
            ->acceptJson()
            ->post(
                $this->baseUrl.'/mpesa/stkpush/v1/processrequest',
                $payload
            );

        if (! $response->successful()) {
            Log::error('M-Pesa STK Push failed', [
                'response' => $response->json(),
            ]);

            throw new RuntimeException('M-Pesa payment request failed.');
        }

        $result = $response->json();

        Log::info('M-Pesa STK Push initiated', [
            'merchant_request_id' => $result['MerchantRequestID'] ?? null,
            'checkout_request_id' => $result['CheckoutRequestID'] ?? null,
        ]);

        return $result;
    }

    /**
     * Normalize a Kenyan phone number to 2547XXXXXXXX / 2541XXXXXXXX format.
     */
    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254'.substr($phone, 1);
        } elseif (str_starts_with($phone, '+254')) {
            $phone = substr($phone, 1);
        } elseif (str_starts_with($phone, '254')) {
            // already in international format
        }

        return $phone;
    }

    /**
     * Validate a normalized Kenyan M-Pesa phone number.
     */
    public function isValidPhone(string $phone): bool
    {
        return (bool) preg_match('/^254[17]\d{8}$/', $phone);
    }

    /**
     * Query the status of an STK Push request from Safaricom Daraja.
     * Used when the callback is delayed or not received.
     */
    public function queryStkPush(string $checkoutRequestId): array
    {
        $timestamp = now()->format('YmdHis');

        $payload = [
            'BusinessShortCode' => $this->getSetting('shortcode'),
            'Password' => $this->generatePassword($timestamp),
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $response = Http::withToken($this->getAccessToken())
            ->acceptJson()
            ->post($this->baseUrl.'/mpesa/stkpushquery/v1/query', $payload);

        if (! $response->successful()) {
            Log::error('M-Pesa STK Push query failed', [
                'checkout_request_id' => $checkoutRequestId,
                'response' => $response->json(),
            ]);

            throw new RuntimeException('Unable to query M-Pesa transaction status.');
        }

        $result = $response->json();

        Log::info('M-Pesa STK Push query result', [
            'checkout_request_id' => $checkoutRequestId,
            'result_code' => $result['ResultCode'] ?? null,
            'result_desc' => $result['ResultDesc'] ?? null,
        ]);

        return $result;
    }
}
