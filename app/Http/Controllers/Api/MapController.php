<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\ItemType;

class MapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getMap()
    {
        $items = ItemType::all();

        $map_items = [];

        foreach($items as $item)
        {
            if(rand(1,10000)/100 <= $item->find_chance)
            {
                $map_items[] = [
                    'id' => $item->id,
                    'icon' => $item->icon,
                    'height' => $item->height,
                    'width' => $item->width,
                    'count' => rand($item->find_min, $item->find_max)
                ];
            }
        }

        $user = User::where(function($query)
        {
            $query->where('human', true)
            ->orWhere('coins', '>', 0);
        })->where('website_count', '>', 0)->orderByRaw("RANDOM()")->first();

        $site = $user->websites()->where('enabled', true)->orderByRaw("RANDOM()")->first(['id', 'url']);

        $map = $site->toArray();
        $map['items'] = $map_items;

        return $map;
    }
}
