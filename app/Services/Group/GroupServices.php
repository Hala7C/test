<?php

namespace App\Services\Group;

use App\Models\Document;
use App\Models\Group;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\Groups\GroupRepository;
use Throwable;

class GroupServices implements GroupRepository
{
    public function index()
    {
        //
        $id = Auth::id();
        $user = User::where('id', $id)->firstOrFail();
        $groups = $user->groups()->paginate(4);
        return $groups;
    }
    public function allGroup()
    {

        $groups = Group::all();
        return $groups;
    }

    public function store($name)
    {
        $id = Auth::id();
        try {
            DB::beginTransaction();
            $input = ['name' => $name];
            $group = Group::create($input);
            DB::table('members')->insert([
                'user_id' => $id,
                'group_id' => $group->id,
                'join_date' => Carbon::now(),
                'group_role' => 'admin',
            ]);
            DB::commit();
            $data = ['data' => $group];
            $status = 200;
            return $response = ['data' => $data, 'status' => $status];
        } catch (Throwable $e) {

            DB::rollBack();
            $data = ['message' => 'Sorry You cant create new group something is error!'];
            $status = 401;
            return $response = ['data' => $data, 'status' => $status];
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showMemberCanAdd($id)
    {
        //
        $group = Group::findOrFail($id);
        if ($group->id != 1) {
            $members_id = $group->users->pluck('id')->toArray();
            $users = DB::table('users')
                ->whereNotIn('id', $members_id)
                ->get();
            $data = ['data' => $users];
            $status = 200;
            return $response = ['data' => $data, 'status' => $status];
        }
        $data = ['message' => 'This is the public group'];
        $status = 401;
        return $response = ['data' => $data, 'status' => $status];
    }
    public function showMemberOfGroup($id)
    {
        $group = Group::find($id);
        if ($group->id != 1) {
            $members  = $group->users()->paginate(6);
            $data = ['data' => $members];
            $status = 200;
            return $response = ['data' => $data, 'status' => $status];
        }
        $data = ['message' => 'This is public group all user in it'];
        $status = 401;
        return $response = ['data' => $data, 'status' => $status];
    }
    public function addMemberToGroup($member_id, $group_id)
    {
        $user_id = Auth::id();
        if ($group_id != 1) {
            $new_member = User::findOrFail($member_id);
            $role = Member::where('group_id', $group_id)->where('user_id', $user_id)->first();
            if ($role->group_role == 'admin') {
                DB::table('members')->insert([
                    'user_id' => $member_id,
                    'group_id' => $group_id,
                    'join_date' => Carbon::now(),
                    'group_role' => 'member',
                ]);
                $data = ['data' => $new_member];
                $status = 201;
                return $response = ['data' => $data, 'status' => $status];
            }
            $data = ['message' => 'You cant add member to this group somethig is error . Blease try again later'];
            $status = 401;
            return $response = ['data' => $data, 'status' => $status];
        }
        $data  = ['message' => 'You cant add member to public group .He is already exist'];
        $status = 401;
        return $response = ['data' => $data, 'status' => $status];
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAllFilesInGroup($id)
    {
        $group = Group::find($id);
        $files = $group->documents()->paginate();
        $data = ['data' => $files];
        $status = 200;
        return $response = ['data' => $data, 'status' => $status];
    }
    public function showAllFilesCanAdd($id)
    {
        $group = Group::find($id);
        $user = Auth::user();
        $documents = $group->documents->pluck('id')->toArray();
        $other_files = Document::whereNotIn('id', $documents)
            ->where('user_id', $user->id)
            ->get();
        $data = ['data' => $other_files];
        $status = 200;
        return $response = ['data' => $data, 'status' => $status];
    }
    public function addFileToGroupe($file_id, $group_id)
    {
        $user = Auth::user();
        $document = Document::findOrFail($file_id);
        if ($user->id == $document->user_id) {
            DB::table('document_group')->insert([
                'group_id' => $group_id,
                'document_id' => $document->id,
            ]);
            $data = ['data' => $document];
            $status = 200;
            return $response = ['data' => $data, 'status' => $status];
        }
        $data = ['message' => 'Sorry you cant add this file to group something is error!!'];
        $status = 401;
        return $response = ['data' => $data, 'status' => $status];
    }
    public function deleteFileFromGroupe($id, $file_id)
    {
        $user = Auth::user();
        $document = Document::findOrFail($file_id);
        if ($user->id == $document->user_id) {
            DB::table('document_group')->where('document_id', $document->id)
                ->where('group_id', $id)->delete();
            $data = ['data' => $document, 'message' => 'file deleted successfuly :)'];
            $status = 202;
            return $response = ['data' => $data, 'status' => $status];
        }
        $data = ['message' => 'You cant delete this file'];
        $status = 401;
        return $response = ['data' => $data, 'status' => $status];
    }

    public function deleteGroup($id)
    {
        $group = Group::find($id);
        if ($group->id != 1) {
            $documents = $group->documents()->get();
            $count = count($documents);
            $free_file_count = 0;
            foreach ($documents as $document) {
                if ($document->status == 'free')
                    $free_file_count++;
            }
            if ($free_file_count == $count) {
                $group->delete();
                $group->users()->detach();
                $data = ['data' => $group, 'message' => 'Group deleted successfuly :)'];
                $status = 202;
                return $response = ['data' => $data, 'status' => $status];
            }
            $data = ['message' => 'Group cant delete . There are files still blocked '];
            $status = 401;
            return $response = ['data' => $data, 'status' => $status];
        }
        $data = ['message' => 'You cant delete the public group'];
        $status = 401;
        return $response = ['data' => $data, 'status' => $status];
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteMember($id, $member_id)
    {
        $user_id = Auth::id();
        $role = Member::where('group_id', $id)->where('user_id', $user_id)->first();
        if ($role->group_role == 'admin') {
            $group = Group::findOrFail($id);
            if ($group->id != 1) {
                $document_member_free = 0;
                $documents = $group->documents;
                $count = count($documents);
                foreach ($documents as $document) {
                    if ($document->status == 'free' || ($document->status == 'booked' && $document->latestReservations()->user_id != $member_id)) {
                        $document_member_free++;
                    }
                }
                if ($count == $document_member_free) {
                    DB::table('members')->where('user_id', $member_id)
                        ->where('group_id', $id)->delete();
                    $member_deleted = User::where('id', $member_id)->first();
                    $data = ['data' => $member_deleted, 'message' => 'You are delete member successfly :)'];
                    $status = 202;
                    return $response = ['data' => $data, 'status' => $status];
                }
                $data = ['message' => "You cannot delete this member because he is still blocking a file"];
                $status = 401;
                return $response = ['data' => $data, 'status' => $status];
            }
            $data = ['message' => 'You cant delete member from public group'];
            $status = 401;
            return $response = ['data' => $data, 'status' => $status];
        }
        $data = ['message' => 'You cant delete member You are not admin :('];
        $status = 401;
        return $response = ['data' => $data, 'status' => $status];
    }
}
