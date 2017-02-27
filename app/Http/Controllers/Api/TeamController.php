<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Team;
use App\User;

class TeamController extends Controller
{
    public function getTeams(User $user)
    {
        $user_team = $user->team()->first(['id']);

        return [
            'my_team' => $user_team ? $user_team->id : null,
            'teams' => Team::all(['id', 'name', 'user_count']),
        ];
    }

    public function getTeam($id, user $user)
    {
        $team = Team::findOrFail($id, ['id', 'name', 'description', 'user_count', 'owner_id']);
        $team->member = $team->isMember($user);
        
        $data = [
            "team" => $team,
            "users" => $team->users()->get(['id', 'name', 'email'])
        ];

        foreach ($data["users"] as $user) {
            $user->gravatar = md5(strtolower($user->email));
            unset($user->email);
        }

        return $data;
    }

    public function joinTeam(Request $request, User $user)
    {
        $team = Team::findOrFail($request->input('team'), ['id', 'owner_id']);

        if(!$team->isOwnedBy($user)) return;

        $target = User::findOrFail($request->input('user'));

        if($target->team) return;

        $target->team()->associate($team);
        $target->save();
        
        $team->increment('user_count');
    }

    public function leaveTeam(User $user)
    {
        if(!$user->team) return;

        $user->team()->dissociate();
        $user->save();

        $user->team->decrement('user_count');
    }

    public function deleteTeam(Request $request, User $user)
    {
        if($user->isOwnerOfTeam()) return;

        $user->team->delete();
    }

    public function newTeam(Request $request, User $user)
    {
        if($user->team) return;

        $this->validate($request, ['name' => 'required|min:2']);

        $team = Team::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'user_count' => 1,
            'user_id' => $user->id
        ]);

        $user->team()->associate($team);
        $user->save();

        return [
            'id' => $team->id,
            'name' => $team->name,
            'user_count' => 1
        ];
    }

    public function updateDesc(Request $request, User $user)
    {
        if(!$user->isOwnerOfTeam) return;

        $user->team->description = $request->input('description');
    }
}
