<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\JsonResponse; // Added this line
use Stripe\Stripe;
use Stripe\Checkout\Session;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str; // Removed duplicate Illuminate\Http\Request

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'nullable|string',
            'return_url' => 'nullable|url',
        ]);

        $buyer = auth('buyer')->user();
        $order = $buyer->orders()->findOrFail($request->order_id);

        if ($order->status !== 'pending') {
            return JsonResponse::error('Order is not pending payment', null, 400);
        }

        // Create Payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'buyer_id' => $buyer->id,
            'provider' => 'stripe',
            'status' => 'pending',
            'transaction_id' => Str::random(32), // Placeholder for Stripe Session ID
        ]);

        // Integrate with Stripe here
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Order #' . $order->id,
                        ],
                        'unit_amount' => intval($order->amount * 100),
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $request->return_url ?? config('app.url') . '/my-tickets?success=true',
            'cancel_url' => $request->return_url ?? config('app.url') . '/my-tickets?cancel=true',
            'metadata' => [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
            ],
        ]);
        $payment->transaction_id = $session->id;
        $payment->save();

        return JsonResponse::created('Payment session created', [
            'payment_id' => $payment->id,
            'order_id' => $order->id,
            'provider' => 'stripe',
            'status' => 'pending',
            'amount' => $order->amount,
            'checkout_url' => $session->url,
            'session_id' => $session->id,
            'expires_at' => now()->addHour(),
        ]);
    }

    public function webhook(Request $request)
    {
        // Verify Stripe signature (optional: add security)
        $payload = $request->all();
        $eventType = $payload['type'] ?? null;
        if ($eventType === 'checkout.session.completed') {
            $session = $payload['data']['object'] ?? [];
            $transactionId = $session['id'] ?? null;
            $payment = Payment::where('transaction_id', $transactionId)->first();
            if ($payment) {
                $payment->status = 'completed';
                $payment->save();
                $order = $payment->order;
                if ($order) {
                    $order->status = 'completed';
                    $order->save();
                    // Update tickets to valid
                    $order->tickets()->update(['status' => 'valid']);
                }
            }
        }
        return JsonResponse::success('Webhook processed');
    }

    public function show(Request $request, string $id)
    {
        $payment = Payment::with('order')->where('buyer_id', auth('buyer')->user()->id)->findOrFail($id);

        return JsonResponse::success('Payment retrieved successfully', $payment);
    }
}
