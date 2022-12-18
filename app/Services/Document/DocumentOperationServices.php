<?php

namespace App\Services\Document;

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
use App\Repositories\Documents\DocumentOperationRepository;
use Illuminate\Support\Facades\Validator;

class DocumentOperationServices implements DocumentOperationRepository

{

    public function readFile($file_id)
    {
        $document = Document::find($file_id);
        $user = User::find($document->user_id);
        $lastReservation=Document::find($file_id)->latestReservation()->first();
        $bookedUser=false;
        if($lastReservation != null){
            if($lastReservation->user_id ==$user->id){
                $bookedUser=true;
            }
        }

        $data = collect();
        if($bookedUser){
            $data->push([
                'id' => $document->id,
                'name' => $document->name,
                'path' => $document->path,
                'owner_id' => $user->id,
                'owner_name' => $user->name,
                'status' => $document->status,
                'group_id' => $document->group_id,
            ]);
            $data = ['data' => $data];
            $status = 210;
            return ['data' => $data, 'status' => $status];
        }
        if ($document->status == 'free' ) {
            $data->push([
                'id' => $document->id,
                'name' => $document->name,
                'path' => $document->path,
                'owner_id' => $user->id,
                'owner_name' => $user->name,
                'status' => $document->status,
                'group_id' => $document->group_id,
            ]);
            $data = ['data' => $data];
            $status = 210;
            return ['data' => $data, 'status' => $status];
        }

        $data = ['message' => 'file booked'];
        $status = 400;
        return ['data' => $data, 'status' => $status];
    }
    public function editFile($file, $fileId)
    {
        $msg = '';
        $status = 400;
        $user = Auth::user();
        $document = Document::lockForUpdate()->find($fileId);
        // $l=User::find($user->id)->latestReservation();
        $l = Reservation::where('user_id', $user->id)->where('document_id', $document->id)->latest('created_at')->first();
        if ($document->status == 'booked') {
            if ($l != null) {
                $fileName = $file->getClientOriginalName();
                if ($fileName == $document->name) {
                    Validator::make([$file], [
                        'file' => 'required|mimes:pdf,xlx,csv|max:2048',
                    ]);
                } else {
                    Validator::make([$file], [
                        'file' => 'required|mimes:pdf,xlx,csv|max:2048|unique:documents,name',
                    ]);
                        $status = 400;
                        return ['data' => ' duplicate entry! this file is already exist !! ', 'status' => $status];


                }
                $file->move(public_path('uploads'), $fileName);
                $document->name = $fileName;
                $document->path = 'uploads/' . $fileName;
                $document->save();
                event('Edit.register', $l->id);
                $msg = 'You edit the documents successfully';
                $status = 210;
            } else {
                $msg = 'You do not have reservation on this file';
                $status = 400;
            }
        } else {

            $msg = 'File not Booked!!You must check in the file before edit it';
            $status = 400;
        }
        return ['data' =>  $msg, 'status' => $status];
    }


    public function bulkCheckIn($files)
    {
        $msg = 'Fail';
        $status = 400;
        $x=0;
            DB::beginTransaction();
            foreach ($files  as $f) {
                $document = Document::find($f);
                if ($document->status == 'free') {
                    $document->status = 'booked';
                    $document->save();
                    Reservation::create([
                        'user_id' => Auth::user()->id,
                        'document_id' => $document->id,
                        'date' => Carbon::now()->setTimezone("GMT+3")->format('Y-m-d H:i:s')
                    ]);
                }else{
                        ++$x;
                }

            }
            if($x==0){
                DB::commit();
                $msg = 'booked successfully';
                $status = 210;
            }
            else if($x>0){
                DB::rollBack();
                $msg='found booked files between your request ';
                $status = 400;
            }


        return ['data' => $msg, 'status' => $status];
    }
    public function CheckIn($document_id)
    {
        DB::beginTransaction();
        try {
            $document = Document::find($document_id);
            if ($document->status == 'free') {
                $document->status = 'booked';
                $document->save();
                Reservation::create([
                    'user_id' => Auth::user()->id,
                    'document_id' => $document_id,
                    'date' => Carbon::now()->setTimezone("GMT+3")->format('Y-m-d H:i:s')
                ]);
                DB::commit();
                $data = ['message' => 'You Booked the document successfully'];
                $status = 210;
                return ['data' => $data, 'status' => $status];
            } else {
                $data = ['message' => 'File is booked'];
                $status = 400;
                return ['data' => $data, 'status' => $status];
            }
        } catch (\Exception $exp) {
            DB::rollBack(); // Tell Laravel, "It's not you, it's me. Please don't persist to DB"
            $data = ['message' => $exp->getMessage(), 'status' => 'failed'];
            $status = 400;
            return ['data' => $data, 'status' => $status];
        }
    }
    public function CheckOut($document_id)
    {
        $document = Document::find($document_id);
        $l=Document::find($document_id)->latestReservation()->get();
        $user=User::find($l->get(0)->user_id);
        // $luser = User::find(Auth::user()->id)->latestReservation();
        if ($document->status == 'booked') {
            if ($user->id == Auth::user()->id) {
                $document->status = 'free';
                $document->save();
                event('CheckOut.register', $l->get(0)->id);
                $data = ['message' => 'You UNBooked the document successfully'];
                $status = 210;
                return ['data' => $data, 'status' => $status];
            } else {
                $data = ['message' => 'You do not have reservation on this file'];
                $status = 400;
                return ['data' => $data, 'status' => $status];
            }
        }
        $data = ['message' => 'File is already free'];
        $status = 400;
        return ['data' => $data, 'status' => $status];
    }
}
