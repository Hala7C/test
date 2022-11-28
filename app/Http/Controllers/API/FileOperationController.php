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

class FileOperationController extends Controller
{

    public function readFile($file_id){
        $document=Document::find($file_id);
        if($document->status=='free'){
            return response()
            ->json(['message' =>'done' ],210);
        }
        return response()
        ->json(['message' =>'file booked' ],400);
    }
    public function editFile(Request $request,$fileId){
        $msg='';
        $status=400;
        $user=Auth::user();
        $document=Document::find($fileId);
        $l=User::find($user->id)->latestReservation();
        if($document->status=='booked'){
            if($l->document_id==$document->id){
                    $this->validate($request,[
                        'file' => 'required|mimes:pdf,xlx,csv|max:2048|unique:documents,name',
                    ]);
                    $fileName = time().'.'.$request->file->extension();
                    $request->file->move(public_path('uploads'), $fileName);
                    $document->name=$fileName;
                    $document->path='uploads/'.$fileName;
                    $document->save();
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

        return response()
                ->json(['message' =>$msg ],$status);
            }

            public function CheckIn($document_id){
                $document=Document::find($document_id);
                if($document->status=='free'){
                    $document->status='booked';
                    $document->save();
                    Reservation::create([
                        'user_id'=>Auth::user()->id,
                        'document_id'=>$document_id,
                        'date'=>Carbon::now()->setTimezone("GMT+3")->format('Y-m-d H:i:s')
                    ]);
                    return response()
                    ->json(['message' =>'You Booked the document successfully' ],210);
                }else{
                    return response()
                    ->json(['message' =>'File is booked' ],400);
                }

                }
                public function CheckOut($document_id){
                    $document=Document::find($document_id);
                    $l=User::find(Auth::user()->id)->latestReservation();
                    if($document->status=='booked'){
                        if($l->document_id==$document_id){
                            $document->status='free';
                            $document->save();
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
