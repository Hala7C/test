<?php

namespace App\Repositories\Documents;

use Illuminate\Http\Request;

interface DocumentOperationRepository
{
    public function readFile($file_id);
    public function editFile($fileName, $fileId);
    public function bulkCheckIn($files);
    public function CheckIn($document_id);
    public function CheckOut($document_id);
}
