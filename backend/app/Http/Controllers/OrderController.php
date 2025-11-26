<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Responses\JsonResponse;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\BoughtTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.ticket_id' => 'required|exists:tickets,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $totalAmount = 0;
                $orderItems = [];
                $buyer = auth('buyer')->user();

                foreach ($request->items as $item) {
                    $ticket = Ticket::lockForUpdate()->find($item['ticket_id']);

                    if ($ticket->quantity < $ticket->boughtTickets()->count() + $item['quantity']) {
                        throw new \Exception("Insufficient tickets available for {$ticket->type}");
                    }

                    $subtotal = $ticket->price * $item['quantity'];
                    $totalAmount += $subtotal;

                    // We don't create BoughtTickets yet, only after payment
                    // But for this simplified flow, we might create them as 'pending' or just store order items
                    // The schema has BoughtTicket linked to Order, so we create them now?
                    // Usually we reserve them. Let's create Order and BoughtTickets with status 'pending' (if status exists on BoughtTicket, yes it does)

                    for ($i = 0; $i < $item['quantity']; $i++) {
                        $orderItems[] = [
                            'ticket_id' => $ticket->id,
                            'price' => $ticket->price, // Store price at time of purchase? BoughtTicket doesn't have price. Order has total amount.
                        ];
                    }
                }

                $order = $buyer->orders()->create([
                    'status' => 'pending',
                    'amount' => $totalAmount,
                ]);

                foreach ($orderItems as $item) {
                    $order->tickets()->create([
                        'ticket_id' => $item['ticket_id'],
                        'buyer_id' => $buyer->id,
                        'status' => 'pending', // Waiting for payment
                        'qr_code' => Str::random(64), // Generate later?
                        'valid_until' => now()->addDays(30), // Placeholder
                    ]);
                }

                return JsonResponse::created('Order created successfully', $order->load('tickets'));
            });
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage(), null, 400);
        }
    }

    public function index(Request $request)
    {
        $orders = auth('buyer')->user()->orders()->withCount('tickets')->paginate($request->query('per_page', 20));

        return JsonResponse::success('Orders retrieved successfully', $orders->items(), 200, [
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'total_pages' => $orders->lastPage(),
                'has_more' => $orders->hasMorePages(),
            ],
        ]);
    }

    public function show(Request $request, string $id)
    {
        $order = auth('buyer')->user()->orders()->with(['tickets.ticket.event', 'payment'])->findOrFail($id);

        return JsonResponse::success('Order retrieved successfully', $order);
    }
}
