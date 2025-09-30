<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;

class PaymentController extends Controller
{
    private $invoiceApi;

    public function __construct()
    {
        // Set Xendit API Key
        Configuration::setXenditKey(config('services.xendit.secret_key'));

        // Initialize Invoice API
        $this->invoiceApi = new InvoiceApi();
    }

    public function createInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::with('user')->findOrFail($request->order_id);

        // Check ownership
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this order'
            ], 403);
        }

        // Check if payment already exists
        if ($order->payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already created for this order',
                'data' => $order->payment
            ], 400);
        }

        try {
            $externalId = 'INV-' . $order->order_number;

            Log::info('Creating Xendit Invoice', [
                'external_id' => $externalId,
                'amount' => $order->total_amount,
                'user_email' => $order->user->email
            ]);

            // Create invoice request object
            $createInvoice = new CreateInvoiceRequest([
                'external_id' => $externalId,
                'amount' => (float) $order->total_amount,
                'description' => "Payment for Order #{$order->order_number}",
                'invoice_duration' => 86400, // 24 hours
                'currency' => 'IDR',
                'customer' => [
                    'given_names' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'success_redirect_url' => config('app.url') . '/payment/success',
                'failure_redirect_url' => config('app.url') . '/payment/failed',
                'items' => [
                    [
                        'name' => 'Order ' . $order->order_number,
                        'quantity' => 1,
                        'price' => (float) $order->total_amount,
                    ]
                ]
            ]);

            // PENTING: Call API untuk create invoice
            $result = $this->invoiceApi->createInvoice($createInvoice);

            Log::info('Xendit Invoice Created Successfully', [
                'invoice_id' => $result['id'],
                'invoice_url' => $result['invoice_url'],
                'status' => $result['status']
            ]);

            // Save payment to database
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'xendit',
                'external_id' => $externalId,
                'invoice_url' => $result['invoice_url'],
                'status' => 'pending',
                'amount' => $order->total_amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => [
                    'payment' => $payment,
                    'invoice_url' => $result['invoice_url'],
                    'expired_at' => $result['expiry_date'] ?? null
                ]
            ], 201);

        } catch (\Xendit\XenditSdkException $e) {
            Log::error('Xendit SDK Exception', [
                'message' => $e->getMessage(),
                'full_error' => $e->getFullError()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage(),
                'error_detail' => $e->getFullError()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to create invoice', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        Log::info('Xendit Webhook Received', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        // Verify webhook token
        $webhookToken = $request->header('x-callback-token');

        if (!$webhookToken) {
            $webhookToken = $request->input('callback_token');
        }

        if (!$webhookToken || $webhookToken !== config('services.xendit.webhook_token')) {
            Log::error('Invalid Xendit Webhook Token', [
                'received_token' => $webhookToken,
                'expected_token' => config('services.xendit.webhook_token')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid webhook token'
            ], 401);
        }

        $externalId = $request->input('external_id');
        $status = $request->input('status');
        $paidAt = $request->input('paid_at');

        Log::info('Processing Xendit Webhook', [
            'external_id' => $externalId,
            'status' => $status
        ]);

        $payment = Payment::where('external_id', $externalId)->first();

        if (!$payment) {
            Log::error('Payment not found', ['external_id' => $externalId]);

            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        // Update payment status
        if ($status === 'PAID') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => $paidAt ? \Carbon\Carbon::parse($paidAt) : now()
            ]);

            $payment->order->update([
                'status' => 'paid'
            ]);

            Log::info('Payment marked as PAID', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id
            ]);

        } elseif ($status === 'EXPIRED') {
            $payment->update([
                'status' => 'expired'
            ]);

            Log::info('Payment marked as EXPIRED', [
                'payment_id' => $payment->id
            ]);

        } elseif ($status === 'FAILED') {
            $payment->update([
                'status' => 'failed'
            ]);

            Log::info('Payment marked as FAILED', [
                'payment_id' => $payment->id
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook processed successfully'
        ]);
    }
}
