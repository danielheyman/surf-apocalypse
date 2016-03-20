<?php

namespace App\Http\Controllers;
use Session;
use App\PmGroup;

class InnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $equips = [];
        foreach($user->equips as $e) {
            $equips[] = $e->sprite();
        }
        uasort($equips, function ($a, $b) { return app("EquipType")->idToPriority($b) - app("EquipType")->idToPriority($a); });
        $equips = array_map(function($e) {
            return app("EquipType")->idToLocation($e);
        }, $equips);
        $equips = implode(',', $equips);
        
        $unreadPm = implode(',', PMGroup::unreadPm($user->id));
        
        Session::put('equips', $equips);
        Session::put('name', $user->name);
        return view('inner', compact('equips', 'user', 'unreadPm'));
    }
}
