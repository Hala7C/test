<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Group;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\Documents\Doc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\Document\DocumentOperationServices;

class FileOperationController extends Controller
{
    private DocumentOperationServices $document_service;
    public function __construct(DocumentOperationServices $document_service)
    {
        $this->document_service = $document_service;
    }
    public function readFile($file_id)
    {
        $response = $this->document_service->readFile($file_id);
        $data = $response['data'];
        $status =  $response['status'];
        return response()->json($data, $status);
    }
    public function editFile(Request $request, $fileId)
    {
        $this->validate($request, ['file' => 'required|mimes:pdf,xlx,csv|max:2048']);
        $file = $request->file('file');
        $response = $this->document_service->editFile($file, $fileId);
        $data = $response['data'];
        $status =  $response['status'];
        return response()->json($data, $status);
    }
    public function bulkCheckIn(Request $request)
    {

        $this->validate($request, [
            'files' => 'required',
        ]);
        $files = $request->post('files');
        $response = $this->document_service->bulkCheckIn($files);
        $data = $response['data'];
        $status =  $response['status'];
        return response()->json($data, $status);
    }

    public function CheckOut($document_id)
    {
        $response = $this->document_service->CheckOut($document_id);
        $data = $response['data'];
        $status =  $response['status'];
        return response()->json($data, $status);
    }
    public function CheckIn($document_id)
    {
        $response = $this->document_service->CheckIn($document_id);
        $data = $response['data'];
        $status =  $response['status'];
        return response()->json($data, $status);
    }
}
