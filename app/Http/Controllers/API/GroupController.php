<?php

namespace App\Http\Controllers\API;

use App\Models\Document;
use App\Models\Group;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\Group\GroupServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Repositories\Groups\GroupRepository;

class GroupController extends Controller
{
    private GroupServices $group_repository;
    public function __construct(GroupServices $group_repository)
    {
        $this->group_repository = $group_repository;
    }
    public function index()
    {
        $group = $this->group_repository->index();
        return response()->json($group, 200);
    }
    public function allGroup()
    {
        $data = $this->group_repository->allGroup();
        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|not_in:public',
        ]);
        $name = $request->post('name');
        $response  = $this->group_repository->store($name);
        $data = $response[0];
        $status =  $response[1];
        return response()->json($data, $status);
    }
    public function showMemberCanAdd($id)
    {
        $response = $this->group_repository->showMemberCanAdd($id);
        $data = $response[0];
        $status =  $response[1];
        return response()->json($data, $status);
    }
    public function showMemberOfGroup($id)
    {
        $response = $this->group_repository->showMemberOfGroup($id);
        $data = $response[0];
        $status =  $response[1];
        return response()->json($data, $status);
    }
    public function addMemberToGroup(Request $request, $id)
    {
        $member_id = $request->post('user_id');
        $response = $this->group_repository->addMemberToGroup($member_id, $id);
        $data = $response[0];
        $status =  $response[1];
        return response()->json($data, $status);
    }
    public function showAllFilesInGroup($id)
    {
        $response = $this->group_repository->showAllFilesInGroup($id);
        $data = $response[0];
        $status =  $response[1];
        return response()->json($data, $status);
    }
    public function showAllFilesCanAdd($id)
    {
        $response = $this->group_repository->showAllFilesCanAdd($id);
        $data = $response[0];
        $status =  $response[1];
        return response()->json($data, $status);
    }
    public function addFileToGroupe(Request $request, $id)
    {
        $file_id = $request->post('file_id');
        $response = $this->group_repository->addFileToGroupe($file_id, $id);
        $data = $response[0];
        $status =  $response[1];
        return response()->json($data, $status);
    }
    public function deleteFileFromGroupe($id, $file_id)
    {
        $response = $this->group_repository->deleteFileFromGroupe($id, $file_id);
        $data = $response['data'];
        $status =  $response['status'];
        return response()->json($data, $status);
    }
    public function deleteGroup($id)
    {
        $response = $this->group_repository->deleteGroup($id);
        $data = $response[0];
        $status =  $response[1];
        return response()->json($data, $status);
    }
    public function deleteMember($id, $member_id)
    {
        $response = $this->group_repository->deleteMember($id, $member_id);
        $data = $response[0];
        $status =  $response[1];
        return response()->json($data, $status);
    }
}
