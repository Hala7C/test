<?php

namespace App\Repositories\Documents;

interface DisplayRepository
{
    public function myFiles();
    public function myGroup();
    public function documentsGroup($group_id);
    public function documentHisory($id);
    public function allDocs();

}
