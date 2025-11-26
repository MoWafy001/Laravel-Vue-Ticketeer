<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BoughtTicket;
use Illuminate\Http\Request;

class BoughtTicketController extends Controller
{
    public function cancelByOrganizer(Request $request, string $bought_ticket_id)
    {
        $ticket = BoughtTicket::findOrFail($bought_ticket_id);

        // Authorization check: ensure organizer owns the event/company
        // $ticket->ticket->event->company->owner_id === $request->user()->id

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $ticket->status = 'cancelled';
        $ticket->save();

        // Trigger refund logic

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket cancelled successfully',
            'data' => [
                'ticket_id' => $ticket->id,
                'status' => 'cancelled',
                'refund_amount' => $ticket->ticket->price, // Simplified
                'cancelled_at' => now(),
                'cancelled_by' => 'organizer',
            ],
        ]);
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $ticket = BoughtTicket::with(['ticket.event', 'buyer'])->where('qr_code', $request->qr_code)->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid ticket',
            ], 404);
        }

        // Check if event is today, etc.

        if ($ticket->status !== 'valid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket is not valid (Status: ' . $ticket->status . ')',
            ], 400);
        }

        if ($ticket->used_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket already used at ' . $ticket->used_at,
            ], 400);
        }

        $ticket->used_at = now();
        $ticket->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket validated successfully',
            'data' => [
                'ticket_id' => $ticket->id,
                'valid' => true,
                'status' => 'valid',
                'used_at' => $ticket->used_at,
                'buyer' => $ticket->buyer,
                'ticket_type' => $ticket->ticket->type,
                'event' => $ticket->ticket->event,
            ],
        ]);
    }
}
