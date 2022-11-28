<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Group;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Display extends Controller
{
    public function myFiles(){

                $user=Auth::user();
                $documents=User::find($user->id)->documents()->get();
                $data=collect();
                $bUser_id= null;
                $bUser_name=null;
                foreach($documents as $d){
                if($d->status=='booked'){
                    $reservation=Document::find($d->id)->latestReservations();
                    $bUser=User::find($reservation->user_id);
                    $bUser_id= $bUser->id;
                    $bUser_name= $bUser->name;
                }
                $data->push([
                    'id'=>$d->id,
                    'name'=>$d->name,
                    'path'=>$d->path,
                    'owner'=>$user->id,
                    'status'=>$d->status,
                    'group_id'=>$d->group_id,
                    'booked_userId'=>$bUser_id,
                    'booked_userName'=>$bUser_name,
                ]);


                }
                return response()->json([
                'data '=>$data
                ],210);
                            }

public function myGroup(){
    $groups=User::find(Auth::user()->id)->group()->get();
    $data=collect();
    foreach($groups as $d){
        $data->push([
            'id'=>$d->id,
            'name'=>$d->name
        ]);
    }
    return response()->json([
        'data '=>$data
        ],210);
}
public function documentsGroup($group_id){
    $documents=Group::find($group_id)->documents()->get();
    $data=collect();
    foreach($documents as $d){
        $data->push([
            'id'=>$d->id,
            'name'=>$d->name,
            'path'=>$d->path,
            'owner'=>$d->user_id,
            'status'=>$d->status,
            'group_id'=>$d->group_id
        ]);
    }
    return response()->json([
        'data '=>$data
        ],210);


    }

    public function documentHisory($id){
            $document=Document::find($id);
            $data=collect();
            $data->push([

            ]);

    }
}
