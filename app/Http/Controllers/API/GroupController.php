<?php

namespace App\Http\Controllers\API;

use App\Models\Document;
use App\Models\Group;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $id = Auth::id();
        $user = User::where('id', $id)->firstOrFail();
        $groups = $user->groups()->paginate(4);
        return response()->json($groups, 200);
    }
    public function store(Request $request)
    {
        $id = Auth::id();
        $request->validate([
            'name' => 'required|string|not_in:public',
        ]);
        try {
            DB::beginTransaction();
            $group = Group::create($request->all());
            DB::table('members')->insert([
                'user_id' => $id,
                'group_id' => $group->id,
                'join_date' => Carbon::now(),
                'group_role' => 'admin',
            ]);
            DB::commit();
            return response()->json($group, 201);
        } catch (Throwable $e) {

            DB::rollBack();
            $message = ['message' => 'Sorry You cant create new group something is error!'];
            return response()->json($message, 404);
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
        $group = Group::find($id);
        $members_id = $group->users->pluck('id')->toArray();
        $users = DB::table('users')
            ->whereNotIn('id', $members_id)
            ->get();
        return response()->json($users, 200);
    }
    public function showMemberOfGroup($id)
    {
        $group = Group::find($id);
        if ($group->id != 1) {
            $members  = $group->users()->paginate(6);
            return response()->json($members, 200);
        }
        $message = ['message' => 'This is public group all user in it'];
        return response()->json($message, 404);
    }
    public function addMemberToGroup(Request $request, $id)
    {
        $user_id = Auth::id();
        if ($id != 1) {
            $member_id = $request->post('user_id');
            $new_member = User::findOrFail($member_id);
            $role = Member::where('group_id', $id)->where('user_id', $user_id)->first();
            if ($role->group_role == 'admin') {
                DB::table('members')->insert([
                    'user_id' => $user_id,
                    'group_id' => $id,
                    'join_date' => Carbon::now(),
                    'group_role' => 'member',
                ]);
                return response()->json($new_member, 201);
            }
            $message = ['message' => 'You cant add member to this group somethig is error . Blease try again later'];
            return response()->json($message, 404);
        }
        $message = ['message' => 'You cant add member to public group .He is already exist'];
        return response()->json($message, 404);
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
        return response()->json($files, 200);
    }
    public function showAllFilesCanAdd($id)
    {
        $group = Group::find($id);
        $user = Auth::user();
        $documents = $group->documents->pluck('id')->toArray();
        $other_files = Document::whereNotIn('id', $documents)
            ->where('user_id', $user->id)
            ->get();
        return response()->json($other_files, 200);
    }
    public function addFileToGroupe(Request $request, $id)
    {
        $user = Auth::user();
        $file_id = $request->post('file_id');
        $document = Document::findOrFail($file_id);
        if ($user->id == $document->user_id) {
            DB::table('document_group')->insert([
                'group_id' => $id,
                'document_id' => $document->id,
            ]);
            return response()->json($document, 201);
        }
        $message = ['message' => 'Sorry you cant add this file to group something is error!!'];
        return response()->json($message, 401);
    }
    public function deleteFileFromGroupe($id, $file_id)
    {
        $user = Auth::user();
        $document = Document::findOrFail($file_id);
        if ($user->id == $document->user_id) {
            DB::table('document_group')->where('document_id', $document->id)
                ->where('group_id', $id)->delete();
            $message = ['message' => 'file deleted successfuly'];
            return response()->json($message, 200);
        }
        $message = ['message' => 'You cant delete this file'];
        return response()->json($message, 401);
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
                $message = ['message' => 'Group deleted successfuly'];
                return response()->json($message, 200);
            }
            $message = ['message' => 'Group cant delete . There are files still blocked '];
            return response()->json($message, 404);
        }
        $message = ['message' => 'You cant delete the public group'];
        return response()->json($message, 404);
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
                    $message = ['message' => 'You are delete member successfly :)'];
                    return response()->json($message, 200);
                }
                $message = ['message' => "You cannot delete this member because he is still blocking a file"];
                return response()->json($message, 401);
            }
            $message = ['message' => 'You cant delete member from public group'];
            return response()->json($message, 401);
        }
        $message = ['message' => 'You cant delete member You are not admin :('];
        return response()->json($message, 401);
    }
}
