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

        $this->joinTeamFromInput($user, $team);
    }

    public function joinTeamFromInput($user, $team)
    {
        $items = $user->items()->get();

        $user->team()->associate($team);
        $user->save();

        foreach($items as $item) {
            $user->giveItem($item->item_type_id, $item->count);
            $item->delete();
        }

        $team->increment('coins', $user->coins);
        $team->increment('user_count');
    }

    public function leaveTeam()
    {
        $user = auth()->user();

        if(!($team = $user->team))
            return;

        $team->decrement('user_count');

        $user->leaveTeam();
    }

    public function deleteTeam(Request $request)
    {
        $user = auth()->user();

        if(!($team = $user->team) || $user->id != $team->owner_id)
            return;

        $team->delete();
    }

    public function newTeam(Request $request)
    {
        $user = auth()->user();

        if($user->team)
            return;

        $this->validate($request, ['name' => 'required|min:2']);

        $house = $user->house();

        $team = Team::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'user_count' => 1,
            'owner_id' => $user->id
        ]);

        $house->owner_id = $team;
        $house->save();

        $this->joinTeamFromInput($user, $team);

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
