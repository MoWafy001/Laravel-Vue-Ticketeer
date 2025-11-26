<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Responses\JsonResponse;
use App\Models\Event;
use App\Models\EventsMember;
use Illuminate\Validation\Rule;

class EventMemberController extends Controller
{
    public function index(Request $request, string $event_id)
    {
        $event = Event::findOrFail($event_id);
        $organizer = auth('organizer')->user();

        // Authorization check
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            if (!$membership || !$membership->can_manage_all_events) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        $members = $event->members()->with('member.organizer')->get();

        return JsonResponse::success('Event members retrieved successfully', $members);
    }

    public function store(Request $request, string $event_id)
    {
        $event = Event::findOrFail($event_id);
        $organizer = auth('organizer')->user();

        // Authorization check
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            if (!$membership || !$membership->can_manage_all_events) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        $request->validate([
            'member_id' => [
                'required',
                'exists:company_members,id',
                Rule::unique('events_members')->where(function ($query) use ($event_id) {
                    return $query->where('event_id', $event_id);
                }),
            ],
            'can_edit_details' => 'boolean',
            'can_manage_tickets' => 'boolean',
            'can_view_analytics' => 'boolean',
            'can_view_buyer_contacts' => 'boolean',
            'can_cancel_tickets' => 'boolean',
            'can_scan_tickets' => 'boolean',
        ]);

        // Verify member belongs to the same company
        // $member = CompanyMember::findOrFail($request->member_id);
        // if ($member->company_id !== $event->company_id) error

        $eventMember = $event->members()->create($request->all());

        return JsonResponse::created('Member assigned to event successfully', $eventMember->load('member.organizer'));
    }

    public function update(Request $request, string $event_id, string $member_id)
    {
        $event = Event::findOrFail($event_id);
        $eventMember = $event->members()->findOrFail($member_id);
        $organizer = auth('organizer')->user();

        // Authorization check
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            if (!$membership || !$membership->can_manage_all_events) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        $request->validate([
            'can_edit_details' => 'boolean',
            'can_manage_tickets' => 'boolean',
            'can_view_analytics' => 'boolean',
            'can_view_buyer_contacts' => 'boolean',
            'can_cancel_tickets' => 'boolean',
            'can_scan_tickets' => 'boolean',
        ]);

        $eventMember->update($request->only([
            'can_edit_details',
            'can_manage_tickets',
            'can_view_analytics',
            'can_view_buyer_contacts',
            'can_cancel_tickets',
            'can_scan_tickets',
        ]));

        return JsonResponse::success('Event member permissions updated successfully', $eventMember);
    }

    public function destroy(Request $request, string $event_id, string $member_id)
    {
        $event = Event::findOrFail($event_id);
        $eventMember = $event->members()->findOrFail($member_id);
        $organizer = auth('organizer')->user();

        // Authorization check
        $isOwner = $organizer->companies()->where('id', $event->company_id)->exists();
        if (!$isOwner) {
            $membership = $organizer->companyMemberships()->where('company_id', $event->company_id)->first();
            if (!$membership || !$membership->can_manage_all_events) {
                return JsonResponse::error('Unauthorized', null, 403);
            }
        }

        $eventMember->delete();

        return JsonResponse::success('Member removed from event successfully');
    }
}
