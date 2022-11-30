<?php

namespace App\Listeners;

use App\Models\Reservation;
use App\Models\Edit_File;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class registEdit
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
        $res=Reservation::find($id);
        Edit_File::create([
            'reservation_id'=>$id,
            'edit_date'=>Carbon::now()->setTimezone("GMT+3")->format('Y-m-d H:i:s'),
        ]);
    }
}
