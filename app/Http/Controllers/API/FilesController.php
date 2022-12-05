<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Document\DocumentServices;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

// $array=$path.spilt("/");
// $name=$array.last;

class FilesController extends Controller
{
    private DocumentServices $document_services;
    public function __construct(DocumentServices $document_services)
    {
        $this->document_services = $document_services;
    }



    public function storeDocument(Request $request)
    {
        $file=$request->file;
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'mimes:pdf,xlx,csv', 'max:2048', Rule::unique('documents', 'name')],
        ]);
        if ($validator->fails()) {
            $data = ['data' => $validator->errors()];
            $status = 400;
        }else{
            $response=$this->document_services->storeDocument($file);
            $data=$response['data'];
            $status =  $response['status'];
        }
        return response()->json($data, $status);
    }




    public function destroyDocument($id)
    {
        $response=$this->document_services->destroyDocument($id);
        $data=$response['data'];
        $status =  $response['status'];
        return response()->json($data, $status);
    }

}
