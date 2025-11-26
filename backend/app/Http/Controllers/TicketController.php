<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Http\Request;

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

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket types retrieved successfully',
            'data' => $tickets,
        ]);
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
            return response()->json([
                'message' => 'The code has already been taken for this event.',
                'errors' => ['code' => ['The code has already been taken for this event.']],
            ], 422);
        }

        $ticket = $event->tickets()->create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket type created successfully',
            'data' => $ticket,
        ], 201);
    }

    public function show(string $id)
    {
        $ticket = Ticket::with('event')->withCount(['boughtTickets as sold'])->findOrFail($id);
        $ticket->available = $ticket->quantity - $ticket->sold;

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket type retrieved successfully',
            'data' => $ticket,
        ]);
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
                return response()->json([
                    'message' => 'The code has already been taken for this event.',
                    'errors' => ['code' => ['The code has already been taken for this event.']],
                ], 422);
            }
        }

        $ticket->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket type updated successfully',
            'data' => $ticket,
        ]);
    }

    public function destroy(string $id)
    {
        $ticket = Ticket::withCount(['boughtTickets as sold'])->findOrFail($id);

        if ($ticket->sold > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete ticket type with sold tickets',
            ], 400);
        }

        $ticket->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket type deleted successfully',
        ]);
    }
}
