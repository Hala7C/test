<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    //
    public function create()
    {
        return view('group.create');
    }
    public function showMemberOfGroup($id)
    {
        $group = Group::find($id);
        $members  = $group->users()->get();
        $members_id = $group->users->pluck('id')->toArray();
        $users = DB::table('users')
            ->whereNotIn('id', $members_id)
            ->get();
        return view('group.show-member', compact('members', 'users', 'group'));
    }
    public function addMemberToGroup(Group $group, Request $request)
    {
        $user = Auth::user();
        $id = $request->post('id');
        $new_user = User::findOrFail($id);
        if ($user->role == 'admin') {
            $new_user->groups()->syncWithPivotValues($group->id, ['join_date' => Carbon::now()]);
        }
        return redirect()->route('group.show.member', $group->id);
    }
    public function index()
    {
        //
        $user = Auth::user();
        $groups = $user->groups;
        return view('group.index', compact('groups'));
    }

    public function show($id)
    {
        $group = Group::find($id);
        $user = Auth::user();
        $files = Document::where('group_id', $group->id)->get();
        //  $other_files = Document::where('group_id', '<>', $id)->get();
        $other_files = Document::where('user_id', '=', $user->id)->where('group_id', '<>', $id)->get();

        return view('group.show', compact('files', 'other_files', 'group'));
    }
    public function addFileToGroupe(Group $group, Request $request)
    {
        $user = Auth::user();
        $id = $request->post('id');
        $document = Document::findOrFail($id);
        if ($user->id == $document->user_id) {
            DB::table('documents')->where('id', $document->id)->update(['group_id' => $group->id]);
        }
        return redirect()->route('group.show', $group->id);
    }
    public function deleteFileFromGroupe(Group $group, $id)
    {
        $user = Auth::user();

        $document = Document::findOrFail($id);
        if ($user->id == $document->user_id && $document->group_id == $group->id) {
            DB::table('documents')->where('id', $document->id)->update(['group_id' => null]);
        }
        return redirect()->route('group.show', $group->id);
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
    }
}
