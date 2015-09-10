<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class MapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getMap()
    {
        $user = User::where(function($query)
        {
            $query->where('human', true)
            ->orWhere('coins', '>', 0);
        })->where('website_count', '>', 0)->orderByRaw("RANDOM()")->first();

        $site = $user->websites()->where('enabled', true)->orderByRaw("RANDOM()")->first(['id', 'url']);

        return $site;
    }
}
