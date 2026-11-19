<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\Tenant;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Show the tenant's pay rent page with outstanding balance.
     */
    public function payRent(Request $request)
    {
        $user = Auth::user();

        if (! $user->hasAnyRole(['tenant'])) {
            abort(403, 'Only tenants can access this page.');
        }

        $tenant = Tenant::where('email', $user->email)->first();

        if (! $tenant) {
            abort(403, 'Tenant record not found.');
        }

        $tenant->load(['unit.property', 'currentLease']);

        $lease = $tenant->currentLease;

        if (! $lease) {
            return view('payments.pay-rent', [
                'tenant' => $tenant,
                'lease' => null,
                'outstandingInvoices' => collect(),
                'totalOutstanding' => 0,
            ]);
        }

        $outstandingInvoices = RentInvoice::where('lease_id', $lease->id)
            ->where('balance', '>', 0)
            ->orderBy('due_date')
            ->get();

        $totalOutstanding = $outstandingInvoices->sum('balance');

        return view('payments.pay-rent', compact(
            'tenant',
            'lease',
            'outstandingInvoices',
            'totalOutstanding'
        ));
    }

    /**
     * Initiate M-Pesa STK Push for rent payment.
     */
    public function payRentStkPush(Request $request, MpesaService $mpesa)
    {
        $user = Auth::user();

        if (! $user->hasAnyRole(['tenant'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only tenants can make payments.',
            ], 403);
        }

        $tenant = Tenant::where('email', $user->email)->first();

        if (! $tenant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant record not found. Please contact support.',
            ], 403);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'phone_number' => ['required', 'string', 'max:20'],
            'rent_invoice_id' => ['nullable', 'exists:rent_invoices,id'],
        ]);

        // Normalize and validate the phone number before anything else.
        $rawPhone = $validated['phone_number'];
        $normalizedPhone = $mpesa->normalizePhone($rawPhone);

        if (! $mpesa->isValidPhone($normalizedPhone)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Kenyan phone number. Use format 07XXXXXXXX or 2547XXXXXXXX.',
            ], 422);
        }

        $tenant->load(['unit.property', 'currentLease']);
        $lease = $tenant->currentLease;

        if (! $lease) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active lease found.',
            ], 400);
        }

        // If a specific invoice is provided, validate it belongs to this tenant's lease
        $invoice = null;
        if ($validated['rent_invoice_id']) {
            $invoice = RentInvoice::where('id', $validated['rent_invoice_id'])
                ->where('lease_id', $lease->id)
                ->first();

            if (! $invoice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid rent invoice.',
                ], 400);
            }

            // Don't allow paying more than the outstanding balance on a specific invoice
            if ($validated['amount'] > $invoice->balance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Amount exceeds outstanding balance for this invoice.',
                ], 400);
            }
        } else {
            // When no specific invoice is selected, cap the amount at the total outstanding balance.
            $totalOutstanding = RentInvoice::where('lease_id', $lease->id)
                ->where('balance', '>', 0)
                ->sum('balance');

            if ($validated['amount'] > $totalOutstanding) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Amount exceeds your outstanding rent balance of KSh '.number_format($totalOutstanding, 2).'.',
                ], 400);
            }
        }

        // Check for duplicate STK Push (pending payment within last 10 minutes)
        $existingPending = Payment::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if ($existingPending) {
            return response()->json([
                'status' => 'error',
                'message' => 'A payment is already being processed. Please wait for it to complete.',
                'payment_id' => $existingPending->id,
            ], 409);
        }

        // Check for duplicate M-Pesa transaction (same checkout request within last 10 minutes)
        $existingMpesa = MpesaTransaction::where('tenant_id', $tenant->id)
            ->where('status', 'initiated')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if ($existingMpesa) {
            return response()->json([
                'status' => 'error',
                'message' => 'An M-Pesa payment is already being processed.',
                'payment_id' => $existingMpesa->payload['payment_id'] ?? null,
            ], 409);
        }

        if (! $mpesa->hasCredentials()) {
            return response()->json([
                'status' => 'error',
                'message' => 'M-Pesa is not configured on this system. Please contact support.',
            ], 500);
        }

        try {
            $accountReference = 'RENT-'.$tenant->id.'-'.strtoupper(Str::random(6));
            $description = 'Rent payment - Unit '.($lease->unit?->unit_number ?? 'N/A');

            $response = $mpesa->stkPush(
                phone: $normalizedPhone,
                amount: (float) $validated['amount'],
                accountReference: $accountReference,
                description: $description
            );

            $checkoutRequestId = $response['CheckoutRequestID'] ?? null;
            $merchantRequestId = $response['MerchantRequestID'] ?? null;

            if (! $checkoutRequestId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'M-Pesa did not return a checkout request ID. Please try again.',
                ], 500);
            }

            // Create payment record with pending status inside a transaction for consistency.
            $payment = DB::transaction(function () use (
                $tenant,
                $lease,
                $invoice,
                $validated,
                $normalizedPhone,
                $accountReference,
                $description,
                $checkoutRequestId,
                $merchantRequestId,
                $response
            ) {
                $payment = Payment::create([
                    'tenant_id' => $tenant->id,
                    'lease_id' => $lease->id,
                    'rent_invoice_id' => $invoice?->id,
                    'amount' => $validated['amount'],
                    'payment_date' => now(),
                    'payment_method' => 'Mpesa',
                    'phone_number' => $normalizedPhone,
                    'transaction_reference' => $accountReference,
                    'checkout_request_id' => $checkoutRequestId,
                    'merchant_request_id' => $merchantRequestId,
                    'payment_type' => 'rent',
                    'status' => 'pending',
                    'notes' => 'STK Push initiated for rent payment',
                    'callback_payload' => null,
                ]);

                // Create M-Pesa transaction record (separate from confirmed rent payment)
                MpesaTransaction::create([
                    'tenant_id' => $tenant->id,
                    'transaction_id' => null,
                    'amount' => $validated['amount'],
                    'checkout_request_id' => $checkoutRequestId,
                    'merchant_request_id' => $merchantRequestId,
                    'result_code' => null,
                    'result_desc' => 'STK Push initiated',
                    'transaction_type' => 'stk_push',
                    'status' => 'pending',
                    'payload' => [
                        'phone_number' => $normalizedPhone,
                        'account_reference' => $accountReference,
                        'description' => $description,
                        'checkout_request_id' => $checkoutRequestId,
                        'merchant_request_id' => $merchantRequestId,
                        'initiated_at' => now()->toDateTimeString(),
                        'payment_id' => $payment->id,
                        'daraja_response' => $response,
                    ],
                ]);

                return $payment;
            });

            Log::info('STK Push initiated for tenant payment', [
                'tenant_id' => $tenant->id,
                'payment_id' => $payment->id,
                'checkout_request_id' => $checkoutRequestId,
                'amount' => $validated['amount'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'M-Pesa STK push initiated. Please check your phone and enter your PIN to complete the payment.',
                'data' => [
                    'payment_id' => $payment->id,
                    'checkout_request_id' => $checkoutRequestId,
                    'merchant_request_id' => $merchantRequestId,
                    'amount' => $validated['amount'],
                    'phone_number' => $normalizedPhone,
                    'status' => 'pending',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('STK Push error for tenant payment', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Query the status of an STK Push transaction (for delayed/missing callbacks).
     */
    public function queryStkPushStatus(Request $request, MpesaService $mpesa)
    {
        $user = Auth::user();

        if (! $user->hasAnyRole(['tenant'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only tenants can query payment status.',
            ], 403);
        }

        $tenant = Tenant::where('email', $user->email)->first();

        if (! $tenant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant record not found.',
            ], 403);
        }

        $validated = $request->validate([
            'checkout_request_id' => ['required', 'string'],
        ]);

        $mpesaTransaction = MpesaTransaction::where('tenant_id', $tenant->id)
            ->where('checkout_request_id', $validated['checkout_request_id'])
            ->first();

        if (! $mpesaTransaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found.',
            ], 404);
        }

        // If we already have a final status, return it without calling Daraja.
        if ($mpesaTransaction->status !== 'pending') {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'checkout_request_id' => $mpesaTransaction->checkout_request_id,
                    'status' => $mpesaTransaction->status,
                    'result_code' => $mpesaTransaction->result_code,
                    'result_desc' => $mpesaTransaction->result_desc,
                    'receipt_number' => $mpesaTransaction->transaction_id,
                    'amount' => $mpesaTransaction->amount,
                ],
            ]);
        }

        try {
            $result = $mpesa->queryStkPush($validated['checkout_request_id']);

            $resultCode = $result['ResultCode'] ?? null;
            $resultDesc = $result['ResultDesc'] ?? null;
            // Daraja query API: ResultCode 0 means the STK push was delivered to the phone,
            // not necessarily that the user completed payment. We only treat it as final
            // if there's a definitive outcome.
            $isFinal = in_array($resultCode, [0, 1032, 1033, 1037], true);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'checkout_request_id' => $validated['checkout_request_id'],
                    'daraja_result_code' => $resultCode,
                    'daraja_result_desc' => $resultDesc,
                    'local_status' => $mpesaTransaction->status,
                    'is_final' => $isFinal,
                    'message' => $isFinal
                        ? ($resultCode == 0
                            ? 'Payment request delivered to your phone. Awaiting callback confirmation.'
                            : 'Transaction failed: '.$resultDesc)
                        : 'Payment request delivered. Waiting for callback.',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('STK Push query error', [
                'checkout_request_id' => $validated['checkout_request_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to check transaction status at the moment.',
            ], 500);
        }
    }

    /**
     * Handle M-Pesa callback with validation and duplicate protection.
     */
    public function handleCallback(Request $request)
    {
        $payload = $request->all();
        Log::info('M-Pesa callback received', ['payload' => $payload]);

        $stkCallback = $payload['Body']['stkCallback'] ?? null;

        if (! $stkCallback) {
            Log::warning('M-Pesa callback without stkCallback structure', ['payload' => $payload]);

            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback format']);
        }

        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
        $resultCode = $stkCallback['ResultCode'] ?? null;
        $resultDesc = $stkCallback['ResultDesc'] ?? null;

        if (! $checkoutRequestId) {
            Log::warning('M-Pesa callback without CheckoutRequestID', ['payload' => $payload]);

            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Missing CheckoutRequestID']);
        }

        // Duplicate callback protection - check if already processed
        $existingTransaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)
            ->where('status', '!=', 'pending')
            ->first();

        if ($existingTransaction) {
            Log::warning('Duplicate M-Pesa callback detected', [
                'checkout_request_id' => $checkoutRequestId,
                'existing_status' => $existingTransaction->status,
            ]);

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Already processed']);
        }

        // Determine status
        $isSuccess = ($resultCode == 0);
        $status = $isSuccess ? 'completed' : 'failed';

        // Extract amount and receipt number from callback metadata
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

        // Find the M-Pesa transaction
        $mpesaTransaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)
            ->orWhere('merchant_request_id', $merchantRequestId)
            ->first();

        if (! $mpesaTransaction) {
            Log::warning('M-Pesa transaction not found for callback', [
                'checkout_request_id' => $checkoutRequestId,
                'merchant_request_id' => $merchantRequestId,
            ]);

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Transaction not found']);
        }

        $tenantId = $mpesaTransaction->tenant_id;
        $paymentId = $mpesaTransaction->payload['payment_id'] ?? null;

        // Use database transaction to ensure consistency
        DB::transaction(function () use (
            $mpesaTransaction,
            $paymentId,
            $tenantId,
            $isSuccess,
            $status,
            $resultCode,
            $resultDesc,
            $amount,
            $mpesaReceiptNumber,
            $payload

        ) {
            // Re-fetch within transaction to avoid race conditions
            $mpesaTransaction = MpesaTransaction::find($mpesaTransaction->id);
            if (! $mpesaTransaction || $mpesaTransaction->status !== 'pending') {
                return; // Already processed
            }

            // Update M-Pesa transaction
            $mpesaTransaction->update([
                'transaction_id' => $mpesaReceiptNumber,
                'amount' => $amount ?? $mpesaTransaction->amount,
                'result_code' => (string) $resultCode,
                'result_desc' => $resultDesc,
                'status' => $status,
                'payload' => array_merge($mpesaTransaction->payload ?? [], [
                    'callback_received_at' => now()->toDateTimeString(),
                    'callback_payload' => $payload,
                    'mpesa_receipt_number' => $mpesaReceiptNumber,
                ]),
            ]);

            // Update payment record
            if ($paymentId) {
                $payment = Payment::find($paymentId);
                if ($payment) {
                    $payment->update([
                        'status' => $status,
                        'transaction_id' => $mpesaReceiptNumber,
                        'result_code' => (string) $resultCode,
                        'result_desc' => $resultDesc,
                        'notes' => $payment->notes.' | Callback processed: '.$status,
                    ]);

                    // If successful, update rent invoice balance
                    if ($isSuccess && $payment->rent_invoice_id) {
                        $this->applyPaymentToInvoice($payment, $amount ?? $payment->amount);
                    } elseif ($isSuccess && ! $payment->rent_invoice_id && $payment->lease_id) {
                        // Apply to oldest outstanding invoice for this lease
                        $this->applyPaymentToOldestInvoice($payment, $amount ?? $payment->amount);
                    }

                    // Create notification for tenant
                    AppNotification::create([
                        'tenant_id' => $tenantId,
                        'title' => 'Payment '.ucfirst($status),
                        'message' => $isSuccess
                            ? 'Your payment of KSh '.number_format($amount ?? $payment->amount, 2)." was successful. M-Pesa Receipt: {$mpesaReceiptNumber}"
                            : 'Your payment of KSh '.number_format($amount ?? $payment->amount, 2)." failed. Reason: {$resultDesc}",
                        'type' => 'payment',
                        'channel' => 'in_app',
                        'related_model' => 'payments',
                        'related_id' => $payment->id,
                    ]);
                }
            }
        });

        Log::info('M-Pesa callback processed', [
            'checkout_request_id' => $checkoutRequestId,
            'status' => $status,
            'result_code' => $resultCode,
            'receipt_number' => $mpesaReceiptNumber,
        ]);

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    /**
     * Apply payment amount to a specific rent invoice.
     */
    private function applyPaymentToInvoice(Payment $payment, float $amount): void
    {
        $invoice = RentInvoice::find($payment->rent_invoice_id);
        if (! $invoice) {
            return;
        }

        $newAmountPaid = (float) $invoice->amount_paid + $amount;
        $newBalance = max(0, (float) $invoice->amount - $newAmountPaid);

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'balance' => $newBalance,
            'status' => $newBalance <= 0 ? 'paid' : 'unpaid',
        ]);
    }

    /**
     * Apply payment to the oldest outstanding invoice for the lease.
     */
    private function applyPaymentToOldestInvoice(Payment $payment, float $amount): void
    {
        $invoice = RentInvoice::where('lease_id', $payment->lease_id)
            ->where('balance', '>', 0)
            ->orderBy('due_date', 'asc')
            ->first();

        if (! $invoice) {
            return;
        }

        // Update payment to reference this invoice
        $payment->update(['rent_invoice_id' => $invoice->id]);

        $newAmountPaid = (float) $invoice->amount_paid + $amount;
        $newBalance = max(0, (float) $invoice->amount - $newAmountPaid);

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'balance' => $newBalance,
            'status' => $newBalance <= 0 ? 'paid' : 'unpaid',
        ]);
    }

    /**
     * Show payment receipt.
     */
    public function receipt(Payment $payment)
    {
        $user = Auth::user();

        // Tenants can only view their own receipts
        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();
            if (! $tenant || $payment->tenant_id !== $tenant->id) {
                abort(403, 'You can only view your own payment receipts.');
            }
        } elseif (! $user->hasAnyRole(['admin', 'property_manager', 'owner', 'agent', 'caretaker', 'developer'])) {
            abort(403, 'Unauthorized access.');
        }

        $payment->load(['tenant', 'lease.unit.property', 'rentInvoice']);

        return view('payments.receipt', compact('payment'));
    }

    /**
     * Show payment history for the authenticated tenant.
     */
    public function paymentHistory(Request $request)
    {
        $user = Auth::user();

        if (! $user->hasAnyRole(['tenant'])) {
            abort(403, 'Only tenants can access this page.');
        }

        $tenant = Tenant::where('email', $user->email)->first();

        if (! $tenant) {
            abort(403, 'Tenant record not found.');
        }

        $payments = Payment::where('tenant_id', $tenant->id)
            ->with(['lease.unit.property', 'rentInvoice'])
            ->latest('payment_date')
            ->paginate(15);

        return view('payments.history', compact('tenant', 'payments'));
    }

    /**
     * Download payment receipt as PDF (placeholder - generates view-based receipt).
     */
    public function downloadReceipt(Payment $payment)
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();
            if (! $tenant || $payment->tenant_id !== $tenant->id) {
                abort(403, 'You can only download your own receipts.');
            }
        } elseif (! $user->hasAnyRole(['admin', 'property_manager', 'owner', 'agent', 'caretaker', 'developer'])) {
            abort(403, 'Unauthorized access.');
        }

        $payment->load(['tenant', 'lease.unit.property', 'rentInvoice']);

        return view('payments.receipt', compact('payment'));
    }
}
