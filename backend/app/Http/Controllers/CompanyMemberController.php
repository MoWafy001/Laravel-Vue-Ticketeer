<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\Organizer;
use Illuminate\Validation\Rule;

class CompanyMemberController extends Controller
{
    public function index(Request $request, string $company_id)
    {
        $company = $request->user()->companies()->findOrFail($company_id);

        $members = $company->members()->with('organizer')->paginate($request->query('per_page', 20));

        return response()->json([
            'status' => 'success',
            'message' => 'Members retrieved successfully',
            'data' => $members->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $members->currentPage(),
                    'per_page' => $members->perPage(),
                    'total' => $members->total(),
                    'total_pages' => $members->lastPage(),
                    'has_more' => $members->hasMorePages(),
                ],
            ],
        ]);
    }

    public function store(Request $request, string $company_id)
    {
        $company = $request->user()->companies()->findOrFail($company_id);

        $request->validate([
            'organizer_id' => [
                'required',
                'exists:organizers,id',
                Rule::unique('company_members')->where(function ($query) use ($company_id) {
                    return $query->where('company_id', $company_id);
                }),
            ],
            'can_view_analytics' => 'boolean',
            'can_manage_members' => 'boolean',
            'can_manage_settings' => 'boolean',
            'can_create_events' => 'boolean',
            'can_manage_all_events' => 'boolean',
            'can_manage_wallet' => 'boolean',
        ]);

        $member = $company->members()->create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Member added successfully',
            'data' => $member->load('organizer'),
        ], 201);
    }

    public function show(Request $request, string $company_id, string $member_id)
    {
        $company = $request->user()->companies()->findOrFail($company_id);
        $member = $company->members()->with('organizer')->findOrFail($member_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Member retrieved successfully',
            'data' => $member,
        ]);
    }

    public function update(Request $request, string $company_id, string $member_id)
    {
        $company = $request->user()->companies()->findOrFail($company_id);
        $member = $company->members()->findOrFail($member_id);

        $request->validate([
            'can_view_analytics' => 'boolean',
            'can_manage_members' => 'boolean',
            'can_manage_settings' => 'boolean',
            'can_create_events' => 'boolean',
            'can_manage_all_events' => 'boolean',
            'can_manage_wallet' => 'boolean',
        ]);

        $member->update($request->only([
            'can_view_analytics',
            'can_manage_members',
            'can_manage_settings',
            'can_create_events',
            'can_manage_all_events',
            'can_manage_wallet',
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Member permissions updated successfully',
            'data' => $member,
        ]);
    }

    public function destroy(Request $request, string $company_id, string $member_id)
    {
        $company = $request->user()->companies()->findOrFail($company_id);
        $member = $company->members()->findOrFail($member_id);

        $member->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Member removed successfully',
        ]);
    }
}
