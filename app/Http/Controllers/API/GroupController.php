<?php

namespace App\Http\Controllers\API;

use App\Models\Document;
use App\Models\Group;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $user = Auth::user();
        $groups = $user->groups;
        return response()->json($groups, 200);
    }
    public function store(Request $request)
    {
        $id = Auth::id();
        $request->validate([
            'name' => 'required|string'
        ]);
        $group = Group::create($request->all());
        $user = User::find($id);
        $user->groups()->syncWithPivotValues($group->id, ['join_date' => Carbon::now()]);
        return response()->json($group, 201);
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
        $members  = $group->users()->get();

        return response()->json($members, 200);
    }
    public function addMemberToGroup(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = $request->post('user_id');
        $new_member = User::findOrFail($user_id);
        if ($user->role == 'admin') {
            $new_member->groups()->syncWithPivotValues($id, ['join_date' => Carbon::now()]);
            return response()->json($new_member, 201);
        }
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

        $files = Document::where('group_id', $group->id)->get();
        return response()->json($files, 200);
    }
    public function showAllFilesCanAdd($id)
    {
        $group = Group::find($id);

        $user = Auth::user();
        $other_files = Document::where('group_id', '<>', $id)->orWhere('group_id', null)
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
            DB::table('documents')->where('id', $document->id)->update(['group_id' => $id]);
            return response()->json($document, 201);
        }
    }
    public function deleteFileFromGroupe($id, $file_id)
    {
        $user = Auth::user();
        $document = Document::findOrFail($file_id);
        if ($user->id == $document->user_id && $document->group_id == $id) {
            DB::table('documents')->where('id', $document->id)->update(['group_id' => null]);
        }
        return ['message' => 'file deleted successfuly'];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteGroup($id)
    {
        //
        $group = Group::find($id);
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

            return ['message' => 'Group deleted successfuly'];
        }
        return ['message' => 'Group cant delete '];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteMember($id)
    {
        //

    }
}
