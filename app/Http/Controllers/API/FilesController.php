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
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class FilesController extends Controller
{
    public function storeDocument(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => ['required', 'mimes:pdf,xlx,csv', 'max:2048', Rule::unique('documents', 'name')],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // $fileName = time().'.'.$request->file->extension();
        $fileName = $request->file('file')->getClientOriginalName();

        $request->file->move(public_path('uploads'), $fileName);

        Document::create([
            'name' => $fileName,
            'path' => 'uploads/' . $fileName,
            'status' => 'free',
            'user_id' => Auth::user()->id,

        ]);
        return response()
            ->json(['message' => 'success', 'You have successfully upload file.'], 210);
    }

    public function destroyDocument($id)
    {

        $document = Document::lockForUpdate()->findOrFail($id);
        if ($document->status == 'free' && $document->user_id == Auth::user()->id) {
            if (File::exists($document->path)) {
                File::delete(public_path($document->path));
            }
            $document->delete();
            return response()
                ->json(['message' => 'You have successfully deleted the document'], 200);
        } else {
            return response()
                ->json(['message' => 'Documents is booked now !! Try again later'], 500);
        }
    }





    public function addFile2Group(Request $request, $group_id)
    {
        $this->validate($request, [
            'document_id' => "required",
            'group_id' => "required",
            'name' => "required",
            'path' => "required",
            'status' => "required",
            'user_id' => "required",
        ]);

        $document = Document::find($request->document_id);
        $document->group_id = $group_id;
        $document->save();
    }
}
