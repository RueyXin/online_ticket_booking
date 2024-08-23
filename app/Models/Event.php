<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    protected $table = 'event';

    protected $fillable = [
        'name', 'description', 'start_time', 'end_time', 'total_tickets', 'available_tickets', 'price'
    ];

    public function booking()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Attempt to book a given number of tickets.
     *
     * @param  int  $numberOfTickets
     * @return bool
     */
    public function book(int $numberOfTickets): bool
    {
        return DB::transaction(function () use ($numberOfTickets) {
            // Check if enough tickets are available
            if ($this->available_tickets < $numberOfTickets) {
                return false;
            }

            // Reduce the number of available tickets
            $this->available_tickets -= $numberOfTickets;

            // Save changes to the database
            return $this->save();
        });
    }
}
