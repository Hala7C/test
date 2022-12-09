<?php

namespace App\Services\Document;


use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Group;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Documents\DocumentRepository;
use Throwable;


class DocumentServices implements DocumentRepository
{
    public function storeDocument( $file)
    {

        $fileName =$file->getClientOriginalName();
        $exist=Document::where('name','=',$fileName)->first();
        if($exist == null){
            $file->move(public_path('uploads'), $fileName);
        $document=  Document::create([
            'name' => $fileName,
            'path' => 'uploads/' . $fileName,
            'status' => 'free',
            'user_id' => Auth::user()->id,
        ]);

        $data = ['data' => $document];
        $status = 200;
        return $response = ['data' => $data, 'status' => $status];
        }

        $status=210;
        $data='file is duplited';
        return $response = ['data' => $data, 'status' => $status];
    }
    public function destroyDocument($id){
        $document = Document::lockForUpdate()->findOrFail($id);
        if ($document->status == 'free' && $document->user_id == Auth::user()->id) {
            if (File::exists($document->path)) {
                File::delete(public_path($document->path));
            }
            $document->delete();
            $data=['data' => $document, 'message' => 'document deleted successfuly :)'];
            $status=210;
        } else {
            $data=['message' => 'Documents is booked now !! Try again later'];
            $status=210;
        }
        return $response = ['data' => $data, 'status' => $status];
    }
}
