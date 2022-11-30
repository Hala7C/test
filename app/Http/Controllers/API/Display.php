<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Edit_File;
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
                if($documents !=null){
                    foreach($documents as $d){
                        if($d->status=='booked'){
                            $reservation=Document::find($d->id)->latestReservations();
                            $bUser=User::find($reservation->user_id);
                            $bUser_id= $bUser->id;
                            $bUser_name= $bUser->name;
                        }else{
                            $bUser_id= null;
                            $bUser_name=null;
                        }
                        $data->push([
                            'id'=>$d->id,
                            'name'=>$d->name,
                            'path'=>$d->path,
                            'owner_id'=>$user->id,
                            'owner_name'=>$user->name,
                            'status'=>$d->status,
                            'group_id'=>$d->group_id,
                            'booked_userId'=>$bUser_id,
                            'booked_userName'=>$bUser_name,
                        ]);
                }
                return response()->json([
                    'data '=>$data
                    ],210);
                }else{
                    return response()->json([
                        'data '=>'You do not have any file yet'
                        ],210);
                }

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
    if($documents !=null){
        foreach($documents as $d){
            if($d->status=='booked'){
                $reservation=Document::find($d->id)->latestReservations();
                $bUser=User::find($reservation->user_id);
                $bUser_id= $bUser->id;
                $bUser_name= $bUser->name;
                $owner_name=User::find($d->user_name);
            }else{
                $bUser_id= null;
                $bUser_name=null;
                $owner_name=null;
            }
            $data->push([
                'id'=>$d->id,
                'name'=>$d->name,
                'path'=>$d->path,
                'owner_id'=>$d->user_id,
                'owner_name'=>$owner_name,
                'status'=>$d->status,
                'group_id'=>$d->group_id,
                'booked_userId'=>$bUser_id,
                'booked_userName'=>$bUser_name,
            ]);
    }
    return response()->json([
        'data '=>$data
        ],210);

    }else{
                    return response()->json([
                        'data '=>'there is no file yet'
                        ],210);
                }
    }

    public function documentHisory($id){
            $document=Document::find($id);
            $owner=User::find($document->user_id);
            $data=collect();
            $reservation_array=collect();
            $edit_array=collect();
            $reservations=Document::find($id)->OrderdReservations();
            foreach($reservations as $r){
                $bUser=User::find($r->user_id);
                // $editFiles=Reservation::find($r->id)->Orderededits();
                // $editFiles=Reservation::find($r->id)->edits()->get();
                $editFiles=Edit_File::where('reservation_id',$r->id)->get();
                foreach($editFiles as $s){

                    $edit_array->push([
                    'edit_date'=>$s->edit_date,
                    ]);
                }
                $reservation_array->push([
                    'booking_user_id'=>$bUser->id,
                    'booking_user_name'=>$bUser->name,
                    'checkIn_date'=>$r->created_at,
                    'check_out_date'=>$r->check_out,
                    'edit_array'=>$edit_array,
                ]);
                $edit_array=collect();
            }
            $data->push([
                'document_id'=>$document->id,
                'document_name'=>$document->name,
                'uploaded_date'=>$document->created_at,
                'uploaded_user_id'=>$owner->id,
                'uploaded_user_name'=>$owner->name,
                'reservation_arry'=>$reservation_array,
            ]);

            return response()->json([
                'data'=>$data
            ],210);
    }
}
