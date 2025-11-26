<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Responses\JsonResponse;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = $request->user()->companies()->paginate($request->query('per_page', 20));

        return JsonResponse::success('Companies retrieved successfully', $companies->items(), 200, [
            'pagination' => [
                'current_page' => $companies->currentPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
                'total_pages' => $companies->lastPage(),
                'has_more' => $companies->hasMorePages(),
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

        return JsonResponse::created('Company created successfully', $company);
    }

    public function show(Request $request, string $id)
    {
        $company = $request->user()->companies()->withCount(['members', 'events'])->findOrFail($id);

        return JsonResponse::success('Company retrieved successfully', $company);
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

        return JsonResponse::success('Company updated successfully', $company);
    }

    public function destroy(Request $request, string $id)
    {
        $company = $request->user()->companies()->findOrFail($id);

        $company->delete();

        return JsonResponse::success('Company deleted successfully');
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

        return JsonResponse::success('Analytics retrieved successfully', $analytics);
    }
}
