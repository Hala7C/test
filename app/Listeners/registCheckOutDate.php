<?php

namespace App\Listeners;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class registCheckOutDate
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($id)
    {
        $reservation=Reservation::find($id);
        $reservation->check_out=Carbon::now()->setTimezone("GMT+3")->format('Y-m-d H:i:s');
        $reservation->save();
    }
}
