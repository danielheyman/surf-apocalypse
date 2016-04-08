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
        
        $equips = $user->orderedEquipsString();
        
        $unreadPm = implode(',', PMGroup::unreadPm($user->id));
        
        Session::put('equips', $equips);
        Session::put('name', $user->name);
        return view('inner', compact('equips', 'user', 'unreadPm'));
    }
}
