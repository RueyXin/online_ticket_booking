<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function testConcurrentBookings()
    {
        // Create an event with tickets available
        $event = Event::factory()->create([
            'total_tickets' => 100,
            'available_tickets' => 30,
            'price' => 250.00,
        ]);

        // Create users for booking
        $users = User::factory()->count(5)->create();

        // Array to hold responses
        $responses = [];

        // Begin transaction
        DB::beginTransaction();

        foreach ($users as $user) {
            // Capture each response
            $responses[] = $this->actingAs($user)->postJson('/api/booking', [
                'event_id' => $event->id,
                'quantity' => 5
            ]);
        }

        // Commit transaction
        DB::commit();

        // Filter successful booking
        $successfulBookings = array_filter($responses, function (TestResponse $response) {
            return $response->status() === 201; // Check if the status is 201 Created
        });

        // Assert the number of successful bookings
        $this->assertCount(5, $successfulBookings);
        // Assert that 5 tickets left
        $this->assertEquals(5, $event->fresh()->available_tickets);
    }
}
