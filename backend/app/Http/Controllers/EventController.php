<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\JsonResponse;
use App\Models\Event;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query()->with(['company', 'creator']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('start_time', '<=', $request->end_date);
        }

        if ($request->has('status')) {
            $now = now();
            switch ($request->status) {
                case 'upcoming':
                    $query->where('start_time', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('start_time', '<=', $now)->where('end_time', '>=', $now);
                    break;
                case 'past':
                    $query->where('end_time', '<', $now);
                    break;
                case 'on_sale':
                    $query->where('sale_start_time', '<=', $now)->where('sale_end_time', '>=', $now);
                    break;
            }
        }

        $sort_by = $request->query('sort_by', 'start_time');
        $sort_order = $request->query('sort_order', 'asc');
        $query->orderBy($sort_by, $sort_order);

        $events = $query->paginate($request->query('per_page', 20));

        return JsonResponse::success('Events retrieved successfully', $events->items(), 200, [
            'pagination' => [
                'current_page' => $events->currentPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'total_pages' => $events->lastPage(),
                'has_more' => $events->hasMorePages(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'sale_start_time' => 'required|date',
            'sale_end_time' => 'required|date|after:sale_start_time',
        ]);

        $organizer = auth('organizer')->user();
        $company = $organizer->companies()->find($request->company_id);

        if (!$company) {
            // Check membership if not owner
            $membership = $organizer->companyMemberships()->where('company_id', $request->company_id)->first();
            if (!$membership || (!$membership->can_create_events && !$membership->can_manage_all_events)) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
            // If member, ensure we use the company instance
            $company = \App\Models\Company::find($request->company_id);
        }

        $eventData = $request->all();
        $eventData['created_by'] = $organizer->id;

        $event = $company->events()->create($eventData);

        return JsonResponse::created('Event created successfully', $event->load('creator', 'company'));
    }

    public function show(string $id)
    {
        $event = Event::with(['company', 'creator', 'tickets'])->findOrFail($id);

        return JsonResponse::success('Event retrieved successfully', $event);
    }

    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);
        $organizer = auth('organizer')->user();

        // Check if user is owner of company
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();

        if (!$isOwner) {
            // Check membership permissions
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            if (!$membership || !$membership->can_manage_all_events) {
                // Check if they are an event member with permission
                $eventMember = $event->members()->where('organizer_id', $organizer->id)->first();
                if (!$eventMember || !$eventMember->can_edit_event) {
                    return JsonResponse::error('Unauthorized', null, 403);
                }
            }
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'sale_start_time' => 'nullable|date',
            'sale_end_time' => 'nullable|date|after:sale_start_time',
        ]);

        $event->update($request->all());

        return JsonResponse::success('Event updated successfully', $event->load('creator', 'company'));
    }

    public function destroy(Request $request, string $id)
    {
        $event = Event::findOrFail($id);
        $organizer = auth('organizer')->user();

        // Check if user is owner of company
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();

        if (!$isOwner) {
            // Check membership permissions (only company admins/owners usually delete events, or maybe manage_all_events)
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            if (!$membership || !$membership->can_manage_all_events) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        $event->delete();

        return JsonResponse::success('Event deleted successfully');
    }

    public function analytics(Request $request, string $id)
    {
        $event = Event::findOrFail($id);
        $organizer = auth('organizer')->user();

        // Check authorization (owner, company admin, or event member)
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            $canManageAll = $membership && $membership->can_manage_all_events;

            $eventMember = $event->members()->where('organizer_id', $organizer->id)->first();
            $isEventMember = $eventMember !== null;

            if (!$canManageAll && !$isEventMember) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        // Placeholder analytics
        $analytics = [
            'event_id' => $event->id,
            'event_name' => $event->name,
            'total_tickets' => $event->tickets()->sum('quantity'),
            'tickets_sold' => $event->tickets()->withCount('boughtTickets')->get()->sum('bought_tickets_count'),
            'tickets_available' => $event->tickets()->sum('quantity') - $event->tickets()->withCount('boughtTickets')->get()->sum('bought_tickets_count'),
            'total_revenue' => 0, // Implement revenue calc
        ];

        return JsonResponse::success('Event analytics retrieved successfully', $analytics);
    }

    public function attendees(Request $request, string $id)
    {
        $event = Event::findOrFail($id);
        $organizer = auth('organizer')->user();

        // Check authorization
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            $canManageAll = $membership && $membership->can_manage_all_events;

            $eventMember = $event->members()->where('organizer_id', $organizer->id)->first();
            $canScan = $eventMember && $eventMember->can_scan_tickets; // Assuming scanning implies viewing attendees? Or maybe separate permission.

            if (!$canManageAll && !$eventMember) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        $attendees = $event->boughtTickets()->with('buyer')->paginate($request->query('per_page', 20));

        return JsonResponse::success('Attendees retrieved successfully', $attendees->items(), 200, [
            'pagination' => [
                'current_page' => $attendees->currentPage(),
                'per_page' => $attendees->perPage(),
                'total' => $attendees->total(),
                'total_pages' => $attendees->lastPage(),
                'has_more' => $attendees->hasMorePages(),
            ],
        ]);
    }
}
