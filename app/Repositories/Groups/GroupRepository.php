<?php

namespace App\Repositories\Groups;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface GroupRepository
{

    public function index();
    public function  store(Request $request);
    public function addMemberToGroup($member_id, $group_id);
    public function showMemberOfGroup($id);
    public function showMemberCanAdd($id);
    public function deleteMember($id, $member_id);
    public function deleteFileFromGroupe($id, $file_id);
    public function addFileToGroupe($file_id, $id);
    public function showAllFilesCanAdd($id);
    public function showAllFilesInGroup($id);
    public function deleteGroup($id);
}
