<?php

namespace Database\Seeders;

use App\Models\Buyer;
use App\Models\Organizer;
use App\Models\Company;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\Payment;
use App\Models\BoughtTicket;
use App\Models\CompanyMember;
use App\Models\EventsMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Get current date for relative dates
        $now = Carbon::now();

        // ========================================
        // 1. Create Buyers
        // ========================================
        $this->command->info('Creating buyers...');

        $buyer1 = Buyer::factory()->create([
            'name' => 'Test Buyer',
            'email' => 'buyer@test.com',
            'password' => Hash::make('password123'),
        ]);

        $buyers = Buyer::factory(20)->create();
        $allBuyers = collect([$buyer1])->merge($buyers);

        $this->command->info("✅ Created {$allBuyers->count()} buyers");

        // ========================================
        // 2. Create Organizers
        // ========================================
        $this->command->info('Creating organizers...');

        $organizer1 = Organizer::factory()->create([
            'name' => 'Test Organizer',
            'email' => 'organizer@test.com',
            'password' => Hash::make('password123'),
        ]);

        $organizers = Organizer::factory(10)->create();
        $allOrganizers = collect([$organizer1])->merge($organizers);

        $this->command->info("✅ Created {$allOrganizers->count()} organizers");

        // ========================================
        // 3. Create Companies
        // ========================================
        $this->command->info('Creating companies...');

        $companies = collect();

        // Each organizer creates 1-3 companies
        foreach ($allOrganizers as $organizer) {
            $numCompanies = rand(1, 3);
            for ($i = 0; $i < $numCompanies; $i++) {
                $company = Company::create([
                    'name' => fake()->company() . ' ' . ['Events', 'Productions', 'Entertainment', 'Venues'][rand(0, 3)],
                    'owner_id' => $organizer->id,
                ]);
                $companies->push($company);
            }
        }

        $this->command->info("✅ Created {$companies->count()} companies");

        // ========================================
        // 4. Create Company Members
        // ========================================
        $this->command->info('Creating company members...');

        $memberCount = 0;
        foreach ($companies as $company) {
            // Add 0-3 members to each company
            $numMembers = rand(0, 3);
            $availableOrganizers = $allOrganizers->where('id', '!=', $company->owner_id);

            // Track already added members for this company
            $addedMembers = collect();

            for ($i = 0; $i < $numMembers && $availableOrganizers->count() > 0; $i++) {
                $member = $availableOrganizers->random();

                // Skip if already added to this company
                if ($addedMembers->contains($member->id)) {
                    continue;
                }

                CompanyMember::create([
                    'company_id' => $company->id,
                    'organizer_id' => $member->id,
                    'can_create_events' => rand(0, 1),
                    'can_manage_all_events' => rand(0, 1),
                ]);

                $addedMembers->push($member->id);
                $memberCount++;
            }
        }

        $this->command->info("✅ Created $memberCount company members");

        // ========================================
        // 5. Create Events (Past, Present, Future)
        // ========================================
        $this->command->info('Creating events...');

        $events = collect();
        $eventTypes = [
            'Tech Conference',
            'Music Festival',
            'Art Exhibition',
            'Food Festival',
            'Sports Tournament',
            'Business Summit',
            'Comedy Show',
            'Theater Performance',
            'Charity Gala',
            'Workshop Series',
            'Film Screening',
            'Gaming Convention',
        ];

        foreach ($companies as $company) {
            // Create 2-5 events per company
            $numEvents = rand(2, 5);

            for ($i = 0; $i < $numEvents; $i++) {
                // Distribute events across past, present, and future
                // 30% past, 20% current, 50% future
                $rand = rand(1, 100);

                if ($rand <= 30) {
                    // Past event (ended 1-60 days ago)
                    $eventOffset = -rand(1, 60);
                    $startTime = $now->copy()->addDays($eventOffset)->setHour(rand(9, 18))->setMinute([0, 30][rand(0, 1)]);
                    $endTime = $startTime->copy()->addHours(rand(2, 8));
                } elseif ($rand <= 50) {
                    // Current event (started in past few days, ends in next few days)
                    $startOffset = -rand(0, 3); // Started 0-3 days ago
                    $startTime = $now->copy()->addDays($startOffset)->setHour(rand(9, 18))->setMinute([0, 30][rand(0, 1)]);
                    $endTime = $startTime->copy()->addDays(rand(1, 5))->addHours(rand(2, 8));
                } else {
                    // Future event (1-90 days in future)
                    $eventOffset = rand(1, 90);
                    $startTime = $now->copy()->addDays($eventOffset)->setHour(rand(9, 18))->setMinute([0, 30][rand(0, 1)]);
                    $endTime = $startTime->copy()->addHours(rand(2, 8));
                }

                // Sale starts 30-60 days before event
                $saleStartTime = $startTime->copy()->subDays(rand(30, 60));
                // Sale ends 1 day before event
                $saleEndTime = $startTime->copy()->subDay();

                $event = Event::create([
                    'company_id' => $company->id,
                    'created_by' => $company->owner_id,
                    'name' => $eventTypes[array_rand($eventTypes)] . ' ' . $now->year,
                    'description' => fake()->paragraphs(3, true),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'sale_start_time' => $saleStartTime,
                    'sale_end_time' => $saleEndTime,
                ]);

                $events->push($event);
            }
        }

        $this->command->info("✅ Created {$events->count()} events");

        // ========================================
        // 6. Create Event Members
        // ========================================
        $this->command->info('Creating event members...');

        $eventMemberCount = 0;
        foreach ($events as $event) {
            // Add 0-2 event-specific members
            $numMembers = rand(0, 2);
            $companyMembers = CompanyMember::where('company_id', $event->company_id)->get();

            // Track already added members for this event
            $addedMembers = collect();

            for ($i = 0; $i < $numMembers && $companyMembers->count() > 0; $i++) {
                $member = $companyMembers->random();

                // Skip if already added to this event
                if ($addedMembers->contains($member->id)) {
                    continue;
                }

                EventsMember::create([
                    'event_id' => $event->id,
                    'member_id' => $member->id, // This references company_members table
                    'can_edit_details' => rand(0, 1),
                    'can_manage_tickets' => rand(0, 1),
                    'can_view_analytics' => rand(0, 1),
                    'can_view_buyer_contacts' => rand(0, 1),
                    'can_cancel_tickets' => rand(0, 1),
                    'can_scan_tickets' => 1, // Most can scan
                ]);

                $addedMembers->push($member->id);
                $eventMemberCount++;
            }
        }

        $this->command->info("✅ Created $eventMemberCount event members");

        // ========================================
        // 7. Create Tickets (Ticket Types)
        // ========================================
        $this->command->info('Creating ticket types...');

        $tickets = collect();
        $ticketTypes = [
            ['type' => 'General Admission', 'code' => 'GA', 'price_range' => [20, 50]],
            ['type' => 'VIP', 'code' => 'VIP', 'price_range' => [100, 200]],
            ['type' => 'Early Bird', 'code' => 'EB', 'price_range' => [15, 40]],
            ['type' => 'Student', 'code' => 'STD', 'price_range' => [10, 30]],
            ['type' => 'Premium', 'code' => 'PREM', 'price_range' => [80, 150]],
        ];

        foreach ($events as $event) {
            // Create 2-4 ticket types per event
            $numTicketTypes = rand(2, 4);
            $usedTypes = [];

            for ($i = 0; $i < $numTicketTypes; $i++) {
                $ticketType = $ticketTypes[array_rand($ticketTypes)];

                // Avoid duplicate types
                while (in_array($ticketType['type'], $usedTypes)) {
                    $ticketType = $ticketTypes[array_rand($ticketTypes)];
                }
                $usedTypes[] = $ticketType['type'];

                $ticket = Ticket::create([
                    'event_id' => $event->id,
                    'code' => $ticketType['code'] . '-' . strtoupper(Str::random(4)),
                    'type' => $ticketType['type'],
                    'price' => rand($ticketType['price_range'][0], $ticketType['price_range'][1]),
                    'quantity' => rand(50, 500),
                ]);

                $tickets->push($ticket);
            }
        }

        $this->command->info("✅ Created {$tickets->count()} ticket types");

        // ========================================
        // 8. Create Orders, Payments, and Bought Tickets
        // ========================================
        $this->command->info('Creating orders and bought tickets...');

        $orderCount = 0;
        $boughtTicketCount = 0;

        // Only create orders for past and current events
        $pastAndCurrentEvents = $events->filter(function ($event) use ($now) {
            return $event->start_time <= $now->copy()->addDays(7); // Include events in next 7 days
        });

        foreach ($pastAndCurrentEvents as $event) {
            $eventTickets = $tickets->where('event_id', $event->id);

            // Create 5-20 orders per event
            $numOrders = rand(5, 20);

            for ($i = 0; $i < $numOrders; $i++) {
                $buyer = $allBuyers->random();

                // Calculate total amount and prepare items
                $numTicketTypes = min(rand(1, 3), $eventTickets->count());
                $orderTickets = $eventTickets->random($numTicketTypes);

                $totalAmount = 0;
                $orderItems = [];

                foreach ($orderTickets as $ticket) {
                    $quantity = rand(1, 4); // 1-4 tickets of this type
                    $totalAmount += $ticket->price * $quantity;
                    $orderItems[] = [
                        'ticket' => $ticket,
                        'quantity' => $quantity,
                    ];
                }

                // Create order first (without payment_id initially)
                $order = Order::create([
                    'buyer_id' => $buyer->id,
                    'status' => 'completed',
                    'amount' => $totalAmount,
                ]);

                // Create payment
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'buyer_id' => $buyer->id,
                    'provider' => ['stripe', 'paypal'][rand(0, 1)],
                    'status' => 'completed',
                    'transaction_id' => Str::random(32),
                ]);

                // Update order with payment_id
                $order->payment_id = $payment->id;
                $order->save();

                $orderCount++;

                // Create bought tickets
                foreach ($orderItems as $item) {
                    for ($j = 0; $j < $item['quantity']; $j++) {
                        $isUsed = $event->end_time < $now ? rand(0, 1) : 0; // Only mark as used for past events randomly

                        BoughtTicket::create([
                            'ticket_id' => $item['ticket']->id,
                            'buyer_id' => $buyer->id,
                            'order_id' => $order->id,
                            'status' => 'active',
                            'valid_until' => $event->end_time->copy()->addDays(1),
                            'used_at' => $isUsed ? $event->start_time->copy()->addHours(rand(0, 3)) : null,
                            'qr_code' => Str::random(32),
                        ]);

                        $boughtTicketCount++;
                    }
                }
            }
        }

        $this->command->info("✅ Created $orderCount orders");
        $this->command->info("✅ Created $boughtTicketCount bought tickets");

        // ========================================
        // Summary
        // ========================================
        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Buyers', $allBuyers->count()],
                ['Organizers', $allOrganizers->count()],
                ['Companies', $companies->count()],
                ['Company Members', $memberCount],
                ['Events', $events->count()],
                ['Event Members', $eventMemberCount],
                ['Ticket Types', $tickets->count()],
                ['Orders', $orderCount],
                ['Bought Tickets', $boughtTicketCount],
            ]
        );

        $this->command->newLine();
        $this->command->info('📝 Test Credentials:');
        $this->command->info('   Buyer: buyer@test.com / password123');
        $this->command->info('   Organizer: organizer@test.com / password123');
        $this->command->newLine();

        // Event distribution
        $pastEvents = $events->filter(fn($e) => $e->end_time < $now)->count();
        $currentEvents = $events->filter(fn($e) => $e->start_time <= $now && $e->end_time >= $now)->count();
        $futureEvents = $events->filter(fn($e) => $e->start_time > $now)->count();

        $this->command->info('📅 Event Distribution:');
        $this->command->info("   Past: $pastEvents events");
        $this->command->info("   Current: $currentEvents events");
        $this->command->info("   Future: $futureEvents events");
    }
}
