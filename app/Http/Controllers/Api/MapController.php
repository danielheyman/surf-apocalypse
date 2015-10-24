<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Website;
use App\ItemType;
use Session;

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
        $ids = [
            'id' => null,
            'items' => [],
        ];

        foreach ($items as $item) {
            if (rand(1, 10000) / 100 <= $item->find_chance) {
                $id = str_random(60);
                $count = rand($item->find_min, $item->find_max);
                $ids['items'][$id] = ['id_real' => $item->id, 'count' => $count];
                $map_items[] = [
                    'id' => $id,
                    'icon' => $item->icon,
                    'name' => $item->name,
                    'count' => $count,
                ];
            }
        }

        $user = User::where(function ($query) {
            $query->where('human', true)
            ->orWhere('coins', '>', 0);
        })->where('website_count', '>', 0)->orderByRaw('RANDOM()')->first();

        $site = $user->websites()->where('enabled', true)->orderByRaw('RANDOM()')->first(['id', 'url']);

        $ids['id'] = $site->id;

        $map = $site->toArray();
        $map['items'] = $map_items;
        $map['id'] = md5(auth()->user()->human + $ids['id']);

        Session::put('current_map', $ids);

        return $map;
    }

    public function postMap(Request $request)
    {
        $input = $request->all();
        $currentMap = Session::get('current_map');

        if ($currentMap && md5($currentMap['id']) == $input['id']) {
            $site = Website::find($currentMap['id']);

            if ($site) {
                $site->increment('views_today');
                $site->increment('views_total');
            }

            foreach ($input['items'] as $item) {
                if (isset($currentMap['items'][$item])) {
                    $item = $currentMap['items'][$item];
                    auth()->user()->giveItem($item['id_real'], $item['count']);
                }
            }
        }

        return $this->getMap();
    }
}
