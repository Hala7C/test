<?php

namespace App\Repositories\Documents;
use Illuminate\Http\Request;

interface DocumentRepository
{
    public function storeDocument(Request $request);
    public function destroyDocument($id);

}
