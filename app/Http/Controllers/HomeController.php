<?php

namespace App\Http\Controllers;
use App\PmGroup;
use Session;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            $equips = $user->equipManager()->myEquipsToString();
            $unreadPm = implode(',', PMGroup::unreadPm($user->id));
            $items = json_encode($user->itemManager()->getMyItems());

            Session::put('equips', $equips);
            Session::put('name', $user->name);
            return view('inner', compact('equips', 'user', 'unreadPm', 'items'));
        }

        return view('welcome');
    }
}
