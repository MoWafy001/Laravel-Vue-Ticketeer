<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyMemberController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventMemberController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BoughtTicketController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('organizer/register', [AuthController::class, 'registerOrganizer']);
    Route::post('organizer/login', [AuthController::class, 'loginOrganizer']);
    Route::post('buyer/register', [AuthController::class, 'registerBuyer']);
    Route::post('buyer/login', [AuthController::class, 'loginBuyer']);
    Route::post('logout', [AuthController::class, 'logout']);
});

// Public Event Routes
Route::get('events', [EventController::class, 'index']);
Route::get('events/{id}', [EventController::class, 'show']);
Route::get('events/{event_id}/tickets', [TicketController::class, 'index']); // Public ticket viewing

// Organizer Routes
Route::middleware(['auth:organizer'])->group(function () {
    // Organizer Profile
    Route::get('organizer/profile', [OrganizerController::class, 'profile']);
    Route::put('organizer/profile', [OrganizerController::class, 'updateProfile']);

    // Companies
    Route::apiResource('companies', CompanyController::class);
    Route::get('companies/{id}/analytics', [CompanyController::class, 'analytics']);

    // Company Members
    Route::apiResource('companies.members', CompanyMemberController::class);

    // Events (Organizer actions)
    Route::post('events', [EventController::class, 'store']);
    Route::put('events/{id}', [EventController::class, 'update']);
    Route::delete('events/{id}', [EventController::class, 'destroy']);
    Route::get('events/{id}/analytics', [EventController::class, 'analytics']);
    Route::get('events/{id}/attendees', [EventController::class, 'attendees']);

    // Event Members
    Route::apiResource('events.members', EventMemberController::class);

    // Tickets (Organizer actions)
    Route::post('events/{event_id}/tickets', [TicketController::class, 'store']);
    Route::get('tickets/{id}', [TicketController::class, 'show']);
    Route::put('tickets/{id}', [TicketController::class, 'update']);
    Route::delete('tickets/{id}', [TicketController::class, 'destroy']);

    // Cancel Bought Ticket
    Route::post('organizer/tickets/{bought_ticket_id}/cancel', [BoughtTicketController::class, 'cancelByOrganizer']);

    // Scan Ticket
    Route::post('tickets/scan', [BoughtTicketController::class, 'scan']);
});

// Buyer Routes
Route::middleware(['auth:buyer'])->group(function () {
    // Buyer Profile
    Route::get('buyer/profile', [BuyerController::class, 'profile']);
    Route::put('buyer/profile', [BuyerController::class, 'updateProfile']);

    // Buyer Tickets
    Route::get('buyer/tickets', [BuyerController::class, 'index']);
    Route::get('buyer/tickets/{id}', [BuyerController::class, 'show']);
    Route::get('buyer/tickets/{id}/pdf', [BuyerController::class, 'downloadPdf']);
    Route::post('buyer/tickets/{id}/cancel', [BuyerController::class, 'cancelByBuyer']);

    // Orders
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);

    // Payments
    Route::post('payments/checkout', [PaymentController::class, 'checkout']);
    Route::get('payments/{id}', [PaymentController::class, 'show']);
});

// Webhooks
Route::post('payments/webhook', [PaymentController::class, 'webhook']);
