<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Responses\JsonResponse;
use App\Models\Buyer;
use App\Models\BoughtTicket;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class BuyerController extends Controller
{
    public function profile(Request $request)
    {
        return JsonResponse::success('Profile retrieved successfully', $request->user());
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
                return JsonResponse::error('Invalid current password', null, 422);
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

        return JsonResponse::success('Profile updated successfully', $buyer);
    }

    public function index(Request $request)
    {
        $query = $request->user()->tickets()->with(['ticket.event.company']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Add date filtering logic if needed

        $tickets = $query->paginate($request->query('per_page', 20));

        return JsonResponse::success('Tickets retrieved successfully', $tickets->items(), 200, [
            'pagination' => [
                'current_page' => $tickets->currentPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
                'total_pages' => $tickets->lastPage(),
                'has_more' => $tickets->hasMorePages(),
            ],
        ]);
    }

    public function show(Request $request, string $id)
    {
        $ticket = $request->user()->tickets()->with(['ticket.event.company'])->findOrFail($id);

        return JsonResponse::success('Ticket retrieved successfully', $ticket);
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
            return JsonResponse::error('Ticket cannot be cancelled', null, 400);
        }

        // Check refund deadline logic here

        $ticket->status = 'cancelled';
        $ticket->save();

        // Trigger refund logic here

        return JsonResponse::success('Ticket cancelled and refund initiated', [
            'ticket_id' => $ticket->id,
            'status' => 'cancelled',
            'refund_status' => 'pending',
        ]);
    }
}
