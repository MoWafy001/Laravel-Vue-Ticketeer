<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\JsonResponse; // Added this line

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
        // $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        // $session = $stripe->checkout->sessions->create([...]);

        return JsonResponse::created('Payment session created', [
            'payment_id' => $payment->id,
            'order_id' => $order->id,
            'provider' => 'stripe',
            'status' => 'pending',
            'amount' => $order->amount,
            'checkout_url' => 'https://checkout.stripe.com/test/' . $payment->transaction_id, // Placeholder
            'session_id' => $payment->transaction_id,
            'expires_at' => now()->addHour(),
        ]);
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

        return JsonResponse::success('Webhook processed');
    }

    public function show(Request $request, string $id)
    {
        $payment = Payment::with('order')->where('buyer_id', auth('buyer')->user()->id)->findOrFail($id);

        return JsonResponse::success('Payment retrieved successfully', $payment);
    }
}
