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
        $items = ItemType::where('find_chance', '>', 0)->get();

        $map_items = [];
        $ids = [
            'id' => null,
            'items' => [],
        ];

        foreach ($items as $item) {
            if (rand(1, 10000) / 100 <= $item->find_chance) {
                $id = str_random(60);
                $multiplier = $item->find_decimal ? 100 : 1;
                $count = rand($item->find_min * $multiplier, $item->find_max * $multiplier) / $multiplier;
                $ids['items'][$id] = ['id_real' => $item->id, 'count' => $count];
                $map_items[] = [
                    'id' => $id,
                    'icon' => $item->icon,
                    'name' => $item->name,
                    'count' => $count,
                ];
            }
        }

        $user = User::where('human', true)->where('website_count', '>', 0)->orderByRaw('RANDOM()')->first();

        $site = $user->websites()->where('enabled', true)->orderByRaw('RANDOM()')->first(['id', 'url', 'name']);

        $ids['id'] = auth()->user()->human + $site->id;

        $map = $site->toArray();
        $map['items'] = $map_items;
        $map['id'] = md5($ids['id']);
        $map['user_info'] = array(
            "name" => $user->name,
            "equip" => $user->orderedEquipsString(),
            "id" => $user->id
        );

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
