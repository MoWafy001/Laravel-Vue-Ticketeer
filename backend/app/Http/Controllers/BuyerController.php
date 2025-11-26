<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Buyer;
use App\Models\BoughtTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class BuyerController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Profile retrieved successfully',
            'data' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $buyer = $request->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('buyers')->ignore($buyer->id),
            ],
            'password' => 'nullable|string|min:8',
            'current_password' => 'required_with:email,password',
        ]);

        if ($request->has('current_password')) {
            if (!Hash::check($request->current_password, $buyer->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid current password',
                ], 422);
            }
        }

        if ($request->has('name')) {
            $buyer->name = $request->name;
        }

        if ($request->has('email')) {
            $buyer->email = $request->email;
        }

        if ($request->has('password')) {
            $buyer->password = Hash::make($request->password);
        }

        $buyer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => $buyer,
        ]);
    }

    public function index(Request $request)
    {
        $query = $request->user()->tickets()->with(['ticket.event.company']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Add date filtering logic if needed

        $tickets = $query->paginate($request->query('per_page', 20));

        return response()->json([
            'status' => 'success',
            'message' => 'Tickets retrieved successfully',
            'data' => $tickets->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $tickets->currentPage(),
                    'per_page' => $tickets->perPage(),
                    'total' => $tickets->total(),
                    'total_pages' => $tickets->lastPage(),
                    'has_more' => $tickets->hasMorePages(),
                ],
            ],
        ]);
    }

    public function show(Request $request, string $id)
    {
        $ticket = $request->user()->tickets()->with(['ticket.event.company'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket retrieved successfully',
            'data' => $ticket,
        ]);
    }

    public function downloadPdf(Request $request, string $id)
    {
        $ticket = $request->user()->tickets()->with(['ticket.event.company', 'buyer'])->findOrFail($id);

        // In a real app, generate PDF using a library like dompdf
        // For now, return a dummy response or text

        // $pdf = Pdf::loadView('pdf.ticket', ['ticket' => $ticket]);
        // return $pdf->download('ticket-'.$ticket->id.'.pdf');

        return response('PDF Download Placeholder', 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ticket-' . $ticket->id . '.pdf"',
        ]);
    }

    public function cancelByBuyer(Request $request, string $id)
    {
        $ticket = $request->user()->tickets()->findOrFail($id);

        if ($ticket->status !== 'valid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket cannot be cancelled',
            ], 400);
        }

        // Check refund deadline logic here

        $ticket->status = 'cancelled';
        $ticket->save();

        // Trigger refund logic here

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket cancelled and refund initiated',
            'data' => [
                'ticket_id' => $ticket->id,
                'status' => 'cancelled',
                'refund_status' => 'pending',
            ],
        ]);
    }
}
