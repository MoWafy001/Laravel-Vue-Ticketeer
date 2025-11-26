<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Company;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = $request->user()->companies()->paginate($request->query('per_page', 20));

        return response()->json([
            'status' => 'success',
            'message' => 'Companies retrieved successfully',
            'data' => $companies->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $companies->currentPage(),
                    'per_page' => $companies->perPage(),
                    'total' => $companies->total(),
                    'total_pages' => $companies->lastPage(),
                    'has_more' => $companies->hasMorePages(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $company = $request->user()->companies()->create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Company created successfully',
            'data' => $company,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $company = $request->user()->companies()->withCount(['members', 'events'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Company retrieved successfully',
            'data' => $company,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $company = $request->user()->companies()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $company->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Company updated successfully',
            'data' => $company,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $company = $request->user()->companies()->findOrFail($id);

        $company->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Company deleted successfully',
        ]);
    }

    public function analytics(Request $request, string $id)
    {
        $company = $request->user()->companies()->findOrFail($id);

        // Placeholder for analytics logic
        // In a real app, this would aggregate data from events, tickets, etc.
        $analytics = [
            'total_events' => $company->events()->count(),
            'total_tickets_sold' => 0, // Implement calculation
            'total_revenue' => 0, // Implement calculation
            'active_events' => $company->events()->where('end_time', '>', now())->count(),
            'upcoming_events' => $company->events()->where('start_time', '>', now())->count(),
            'past_events' => $company->events()->where('end_time', '<', now())->count(),
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Analytics retrieved successfully',
            'data' => $analytics,
        ]);
    }
}
