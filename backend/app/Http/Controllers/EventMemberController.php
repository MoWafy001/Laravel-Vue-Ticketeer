<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\EventsMember;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventMemberController extends Controller
{
    public function index(Request $request, string $event_id)
    {
        $event = Event::findOrFail($event_id);

        // Authorization check

        $members = $event->members()->with('member.organizer')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Event members retrieved successfully',
            'data' => $members,
        ]);
    }

    public function store(Request $request, string $event_id)
    {
        $event = Event::findOrFail($event_id);

        // Authorization check

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

        return response()->json([
            'status' => 'success',
            'message' => 'Member assigned to event successfully',
            'data' => $eventMember->load('member.organizer'),
        ], 201);
    }

    public function update(Request $request, string $event_id, string $member_id)
    {
        $event = Event::findOrFail($event_id);
        $eventMember = $event->members()->findOrFail($member_id);

        // Authorization check

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

        return response()->json([
            'status' => 'success',
            'message' => 'Event member permissions updated successfully',
            'data' => $eventMember,
        ]);
    }

    public function destroy(Request $request, string $event_id, string $member_id)
    {
        $event = Event::findOrFail($event_id);
        $eventMember = $event->members()->findOrFail($member_id);

        // Authorization check

        $eventMember->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Member removed from event successfully',
        ]);
    }
}
