<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Team;
use App\User;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getTeams()
    {
        $user_team = auth()->user()->team()->first(['id']);

        return [
            'my_team' => $user_team ? $user_team->id : null,
            'teams' => Team::all(['id', 'name', 'user_count']),
        ];
    }

    public function getTeam($id)
    {
        $team = Team::findOrFail($id, ['id', 'name', 'description', 'user_count', 'owner_id']);

        $data = [
            "team" => $team,
            "users" => $team->users()->get(['id', 'name', 'email'])
        ];

        foreach ($data["users"] as $user) {
            $user->gravatar = md5(strtolower($user->email));
            unset($user->email);
        }

        $data['team']->member = (auth()->user()->team_id == $team->id);

        return $data;
    }

    public function joinTeam(Request $request)
    {
        $team = Team::findOrFail($request->input('team'), ['id', 'owner_id', 'coins']);

        if(auth()->user()->id != $team->owner_id)
            return;

        $user = User::findOrFail($request->input('user'));

        if($user->team)
            return;

        $user->team()->associate($team);
        $user->save();

        $team->increment('health', $user->healthOrStrength);
    }

    public function leaveTeam()
    {
        $user = auth()->user();

        if(!($team = $user->team))
            return;

        $user->team()->dissociate();
        $user->healthOrStrength = floor($team->health / $team->user_count);
        if($user->healthOrStrength <= 0)
            $user->healthOrStrength = 5;
        $user->save();

        $team->health = $team->health - $user->healthOrStrength;
        if($team->health <= 0)
            $team->health = 5;
        $team->save();
    }

    public function deleteTeam(Request $request)
    {
        $user = auth()->user();

        if(!($team = $user->team) || $user->id != $team->owner_id)
            return;

        $health = $team->health / $team->user_count;
        foreach($team->users as $u) {
            $u->healthOrStrength = $health;
            $u->save();
        }
        $team->delete();
    }

    public function newTeam(Request $request)
    {
        $user = auth()->user();

        if($user->team)
            return;

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

    public function updateDesc(Request $request)
    {
        $user = auth()->user();

        if(!($team = $user->team) || $user->id != $team->owner_id)
            return;

        $team->description = $request->input('description');
    }
}
