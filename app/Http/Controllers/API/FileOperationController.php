<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Group;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FileOperationController extends Controller
{

    public function readFile($file_id){
        $document=Document::find($file_id);
        $user=User::find($document->user_id);
        $data=collect();
        if($document->status=='free'){
            $data->push([
                'id'=>$document->id,
                'name'=>$document->name,
                'path'=>$document->path,
                'owner_id'=>$user->id,
                'owner_name'=>$user->name,
                'status'=>$document->status,
                'group_id'=>$document->group_id,]);
            return response()
            ->json(['data' =>$data ],210);
        }
        return response()
        ->json(['message' =>'file booked' ],400);
    }
    public function editFile(Request $request,$fileId){
        $msg='';
        $status=400;
        $user=Auth::user();
        $document=Document::lockForUpdate()->find($fileId);
        // $l=User::find($user->id)->latestReservation();
        $l=Reservation::where('user_id',$user->id)->where('document_id',$document->id)->latest('created_at')->first();
        if($document->status=='booked'){
            if($l!=null){
                    $fileName =$request->file('file')->getClientOriginalName();
                    if($fileName==$document->name){
                        $this->validate($request,[
                            'file' => 'required|mimes:pdf,xlx,csv|max:2048',
                        ]);
                    }else{
                        $this->validate($request,[
                            'file' => 'required|mimes:pdf,xlx,csv|max:2048|unique:documents,name',
                        ]);
                    }
                    $request->file->move(public_path('uploads'), $fileName);
                    $document->name=$fileName;
                    $document->path='uploads/'.$fileName;
                    $document->save();
                    event('Edit.register',$l->id);
                    $msg='You edit the documents successfully';
                    $status=210;
             }else{
                    $msg='You do not have reservation on this file';
                    $status=400;
                }
            }else{

            $msg='File not Booked!!You must check in the file before edit it';
            $status=400;
        }
        return response()
        ->json(['message' =>$msg ],$status);
    }
    public function bulkCheckIn(Request $request){
        $msg='Fail';
        $status=400;
        $this->validate($request,[
            'files'=>'required',
        ]);
        $files = array(
            'files' => $request->files,
        );
        try{
        DB::beginTransaction();
        foreach($request->input('files')  as $f){
            $document=Document::find($f);
            if($document->status=='free'){
                $document->status='booked';
                $document->save();
                Reservation::create([
                    'user_id'=>Auth::user()->id,
                    'document_id'=>$document->id,
                    'date'=>Carbon::now()->setTimezone("GMT+3")->format('Y-m-d H:i:s')
                ]);

            }
            $msg='booked successfully';
            $status=210;
        }
        DB::commit();
        }catch(\Exception $exp){
            DB::rollBack(); // Tell Laravel, "It's not you, it's me. Please don't persist to DB"
            return response([
                'message' => $exp->getMessage(),
                'status' => 'failed'
            ], 400);
        }
        return response()
                ->json(['message' =>$msg ],$status);
            }

            public function CheckIn($document_id){
                try{
                DB::beginTransaction();
                $document=Document::find($document_id);
                if($document->status=='free'){
                    $document->status='booked';
                    $document->save();
                    Reservation::create([
                        'user_id'=>Auth::user()->id,
                        'document_id'=>$document_id,
                        'date'=>Carbon::now()->setTimezone("GMT+3")->format('Y-m-d H:i:s')
                    ]);
                    DB::commit();
                    return response()
                    ->json(['message' =>'You Booked the document successfully' ],210);
                }else{
                    return response()
                    ->json(['message' =>'File is booked' ],400);
                }}catch(\Exception $exp){
                    DB::rollBack(); // Tell Laravel, "It's not you, it's me. Please don't persist to DB"
                    return response([
                        'message' => $exp->getMessage(),
                        'status' => 'failed'
                    ], 400);
                }

                }
                public function CheckOut($document_id){
                    $document=Document::find($document_id);
                    $l=User::find(Auth::user()->id)->latestReservation();
                    if($document->status=='booked'){
                        if($l->document_id==$document_id){
                            $document->status='free';
                            $document->save();
                            event('CheckOut.register',$l->id);
                            return response()
                            ->json(['message' =>'You UNBooked the document successfully' ],210);
                        }
                        else{
                            return response()
                            ->json(['message' =>'You do not have reservation on this file' ],400);
                        }
                    }
                    return response()
                    ->json(['message' =>'File is already free' ],400);
                    }

}
