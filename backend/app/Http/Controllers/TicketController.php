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

        // Publicly accessible? Or only for organizers?
        // The route is under organizer middleware, so we assume organizer context.
        // But maybe buyers also need to see tickets?
        // For now, let's assume this is the organizer's view of tickets.
        // If buyers need it, we'll have a public route.

        $organizer = auth('organizer')->user();

        // Auth check
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            $canManageAll = $membership && $membership->can_manage_all_events;
            $eventMember = $event->members()->where('organizer_id', $organizer->id)->first();

            if (!$canManageAll && !$eventMember) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

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
        $organizer = auth('organizer')->user();

        // Authorization check
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            $canManageAll = $membership && $membership->can_manage_all_events;

            $eventMember = $event->members()->where('organizer_id', $organizer->id)->first();
            $canManageTickets = $eventMember && $eventMember->can_manage_tickets;

            if (!$canManageAll && !$canManageTickets) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        $request->validate([
            'code' => 'required|string|max:50',
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

        $organizer = auth('organizer')->user();
        $event = $ticket->event;

        // Auth check similar to index
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            $canManageAll = $membership && $membership->can_manage_all_events;
            $eventMember = $event->members()->where('organizer_id', $organizer->id)->first();

            if (!$canManageAll && !$eventMember) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        return JsonResponse::success('Ticket type retrieved successfully', $ticket);
    }

    public function update(Request $request, string $id)
    {
        $ticket = Ticket::withCount(['boughtTickets as sold'])->findOrFail($id);
        $event = $ticket->event;
        $organizer = auth('organizer')->user();

        // Authorization check
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            $canManageAll = $membership && $membership->can_manage_all_events;

            $eventMember = $event->members()->where('organizer_id', $organizer->id)->first();
            $canManageTickets = $eventMember && $eventMember->can_manage_tickets;

            if (!$canManageAll && !$canManageTickets) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

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
        $event = $ticket->event;
        $organizer = auth('organizer')->user();

        // Authorization check
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            $canManageAll = $membership && $membership->can_manage_all_events;

            $eventMember = $event->members()->where('organizer_id', $organizer->id)->first();
            $canManageTickets = $eventMember && $eventMember->can_manage_tickets;

            if (!$canManageAll && !$canManageTickets) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        if ($ticket->sold > 0) {
            return JsonResponse::error('Cannot delete ticket type with sold tickets', null, 400);
        }

        $ticket->delete();

        return JsonResponse::success('Ticket type deleted successfully');
    }
}
