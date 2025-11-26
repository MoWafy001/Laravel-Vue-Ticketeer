<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Responses\JsonResponse;
use App\Models\Ticket;
use App\Models\Event;

class TicketController extends Controller
{
    public function index(Request $request, string $event_id)
    {
        $event = Event::findOrFail($event_id);

        $tickets = $event->tickets()
            ->withCount(['boughtTickets as sold'])
            ->get()
            ->map(function ($ticket) {
                $ticket->available = $ticket->quantity - $ticket->sold;
                return $ticket;
            });

        return JsonResponse::success('Ticket types retrieved successfully', $tickets);
    }

    public function store(Request $request, string $event_id)
    {
        $event = Event::findOrFail($event_id);

        // Authorization check needed here

        $request->validate([
            'code' => 'required|string|max:50', // Unique check needed per event, complex validation
            'type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0|max:999999.99',
            'quantity' => 'required|integer|min:1',
        ]);

        // Manual unique check for code within event
        if ($event->tickets()->where('code', $request->code)->exists()) {
            return JsonResponse::error('The code has already been taken for this event.', ['code' => ['The code has already been taken for this event.']], 422);
        }

        $ticket = $event->tickets()->create($request->all());

        return JsonResponse::created('Ticket type created successfully', $ticket);
    }

    public function show(string $id)
    {
        $ticket = Ticket::with('event')->withCount(['boughtTickets as sold'])->findOrFail($id);
        $ticket->available = $ticket->quantity - $ticket->sold;

        return JsonResponse::success('Ticket type retrieved successfully', $ticket);
    }

    public function update(Request $request, string $id)
    {
        $ticket = Ticket::withCount(['boughtTickets as sold'])->findOrFail($id);

        // Authorization check needed here

        $request->validate([
            'code' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0|max:999999.99',
            'quantity' => 'nullable|integer|min:' . $ticket->sold,
        ]);

        if ($request->has('code') && $request->code !== $ticket->code) {
            if ($ticket->event->tickets()->where('code', $request->code)->exists()) {
                return JsonResponse::error('The code has already been taken for this event.', ['code' => ['The code has already been taken for this event.']], 422);
            }
        }

        $ticket->update($request->all());

        return JsonResponse::success('Ticket type updated successfully', $ticket);
    }

    public function destroy(string $id)
    {
        $ticket = Ticket::withCount(['boughtTickets as sold'])->findOrFail($id);

        if ($ticket->sold > 0) {
            return JsonResponse::error('Cannot delete ticket type with sold tickets', null, 400);
        }

        $ticket->delete();

        return JsonResponse::success('Ticket type deleted successfully');
    }
}
