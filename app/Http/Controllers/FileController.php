<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function index()
    {
        return view('fileUpload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,xlx,csv|max:2048',
        ]);

        $fileName = time().'.'.$request->file->extension();

        $request->file->move(public_path('uploads'), $fileName);

        Document::create([
            'name'=>$request->file,
            'path'=>'uploads/'.$fileName,
            'status'=>'free',
            'user_id'=>Auth::user()->id,
            'group_id'=>null
        ]);
        return back()
            ->with('success','You have successfully upload file.')
            ->with('file', $fileName);

    }
}
