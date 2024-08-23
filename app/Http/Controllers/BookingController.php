<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\BookingRequest;
use App\Events\BookingCreated;

class BookingController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/booking",
     *     summary="Book an event ticket",
     *     tags={"Book"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        return Booking::all();
    }

    /**
     * Store a newly created booking in the database.
     *
     * @param  BookingRequest  $request
     * @return JsonResponse
     */
    public function store(BookingRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $event = Event::findOrFail($request->event_id);
            
            // Attempt to book tickets
            if (!$event->book($request->quantity)) {
                return response()->json(['message' => 'Booking failed. Not enough tickets available.'], 409);
            }

            // Create a booking
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'event_id' => $event->id,
                'quantity' => $request->quantity,
                'total_price' => $event->price * $request->quantity
            ]);

            // Broadcast an event for real-time updates
            event(new BookingCreated($booking));

            return response()->json($booking, 201);
        });
    }

    /**
     * Display the specified booking.
     *
     * @param  Booking  $booking
     * @return JsonResponse
     */
    public function show(Booking $booking): JsonResponse
    {
        return response()->json($booking);
    }

    /**
     * Update the specified booking.
     *
     * @param  BookingRequest  $request
     * @param  Booking  $booking
     * @return JsonResponse
     */
    public function update(BookingRequest $request, Booking $booking): JsonResponse
    {
        $booking->update($request->validated());
        return response()->json($booking, 200);
    }

    /**
     * Remove the specified booking from storage.
     *
     * @param  Booking  $booking
     * @return JsonResponse
     */
    public function destroy(Booking $booking): JsonResponse
    {
        $booking->delete();
        return response()->json(null, 204);
    }
}
