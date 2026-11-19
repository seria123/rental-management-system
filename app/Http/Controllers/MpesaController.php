<?php

namespace App\Http\Controllers;

use App\Models\MpesaTransaction;
use App\Models\Tenant;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function stkPush(Request $request, MpesaService $mpesa)
    {
        $validated = $request->validate([
            'tenant_id' => ['nullable', 'exists:tenants,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'phone_number' => ['required', 'string', 'max:20'],
            'account_reference' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:100'],
        ]);

        $tenant = null;
        if (! empty($validated['tenant_id'])) {
            $tenant = Tenant::find($validated['tenant_id']);
        }

        try {
            $response = $mpesa->stkPush(
                phone: $validated['phone_number'],
                amount: (float) $validated['amount'],
                accountReference: $validated['account_reference'] ?? ($tenant?->name ?? 'Payment'),
                description: $validated['description'] ?? 'Payment'
            );

            MpesaTransaction::create([
                'tenant_id' => $tenant?->id,
                'amount' => $validated['amount'],
                'transaction_type' => 'stk_push',
                'status' => 'initiated',
                'checkout_request_id' => $response['CheckoutRequestID'] ?? null,
                'merchant_request_id' => $response['MerchantRequestID'] ?? null,
                'payload' => [
                    'phone_number' => $validated['phone_number'],
                    'response' => $response,
                    'requested_at' => now()->toDateTimeString(),
                ],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'M-Pesa STK push has been initiated.',
                'data' => $response,
            ]);
        } catch (\Throwable $e) {
            Log::error('STK Push error', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $payload = $request->all();
        Log::info('M-Pesa callback received', ['payload' => $payload]);

        // Handle STK Push callback structure
        $stkCallback = $payload['Body']['stkCallback'] ?? null;
        if ($stkCallback) {
            $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
            $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
            $resultCode = $stkCallback['ResultCode'] ?? null;
            $resultDesc = $stkCallback['ResultDesc'] ?? null;

            $transaction = MpesaTransaction::query()
                ->where('checkout_request_id', $checkoutRequestId)
                ->orWhere('merchant_request_id', $merchantRequestId)
                ->first();

            $status = ($resultCode == 0) ? 'completed' : 'failed';

            $amount = null;
            $mpesaReceiptNumber = null;
            if (isset($stkCallback['CallbackMetadata']['Item'])) {
                foreach ($stkCallback['CallbackMetadata']['Item'] as $item) {
                    if (($item['Name'] ?? '') === 'Amount') {
                        $amount = $item['Value'];
                    }
                    if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                        $mpesaReceiptNumber = $item['Value'];
                    }
                }
            }

            if ($transaction) {
                $transaction->update([
                    'transaction_id' => $mpesaReceiptNumber ?? $transaction->transaction_id,
                    'amount' => $amount ?? $transaction->amount,
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc,
                    'status' => $status,
                    'payload' => $payload,
                ]);
            } else {
                MpesaTransaction::create([
                    'transaction_id' => $mpesaReceiptNumber,
                    'checkout_request_id' => $checkoutRequestId,
                    'merchant_request_id' => $merchantRequestId,
                    'amount' => $amount ?? 0,
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc,
                    'status' => $status,
                    'transaction_type' => 'callback',
                    'payload' => $payload,
                ]);
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        // Fallback for other callback formats
        $transaction = MpesaTransaction::query()
            ->where('transaction_id', $payload['TransactionId'] ?? null)
            ->orWhere('checkout_request_id', $payload['CheckoutRequestID'] ?? null)
            ->first();

        if ($transaction) {
            $transaction->update([
                'result_code' => $payload['Result']['Code'] ?? $payload['ResultCode'] ?? $transaction->result_code,
                'result_desc' => $payload['Result']['Desc'] ?? $payload['ResultDesc'] ?? $transaction->result_desc,
                'status' => (($payload['Result']['Code'] ?? $payload['ResultCode'] ?? '0') == '0') ? 'completed' : 'failed',
                'payload' => $payload,
            ]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    public function pay(MpesaService $mpesa)
    {
        $response = $mpesa->stkPush(
            phone: '0712345678',
            amount: 100,
            accountReference: 'RENT001',
            description: 'Rent Payment'
        );

        return response()->json($response);
    }
}
