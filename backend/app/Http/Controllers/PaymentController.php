<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'nullable|string',
            'return_url' => 'nullable|url',
        ]);

        $order = $request->user()->orders()->findOrFail($request->order_id);

        if ($order->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Order is not pending payment',
            ], 400);
        }

        // Create Payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'buyer_id' => $request->user()->id,
            'provider' => 'stripe',
            'status' => 'pending',
            'transaction_id' => Str::random(32), // Placeholder for Stripe Session ID
        ]);

        // Integrate with Stripe here
        // $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        // $session = $stripe->checkout->sessions->create([...]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment session created',
            'data' => [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'provider' => 'stripe',
                'status' => 'pending',
                'amount' => $order->amount,
                'checkout_url' => 'https://checkout.stripe.com/test/' . $payment->transaction_id, // Placeholder
                'session_id' => $payment->transaction_id,
                'expires_at' => now()->addHour(),
            ],
        ], 201);
    }

    public function webhook(Request $request)
    {
        // Verify Stripe signature
        // Handle events like checkout.session.completed

        // For now, simulate success
        $payload = $request->all();

        // Logic to find payment and update status
        // $payment = Payment::where('transaction_id', $payload['data']['object']['id'])->first();
        // if ($payment) {
        //     $payment->status = 'completed';
        //     $payment->save();
        //     $payment->order->status = 'completed';
        //     $payment->order->save();
        //     // Update tickets to valid
        //     $payment->order->tickets()->update(['status' => 'valid']);
        // }

        return response()->json([
            'status' => 'success',
            'message' => 'Webhook processed',
        ]);
    }

    public function show(Request $request, string $id)
    {
        $payment = Payment::with('order')->where('buyer_id', $request->user()->id)->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment retrieved successfully',
            'data' => $payment,
        ]);
    }
}
