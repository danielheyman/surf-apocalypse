<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Website;
use App\ItemType;
use App\ItemTypes;
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
        $map['id'] = md5($ids['id']);

        Session::put('current_map', $ids);

        return $map;
    }

    public function postMap(Request $request)
    {
        $input = $request->all();
        $currentMap = Session::get('current_map');

        if (!$currentMap || md5($currentMap['id']) != $input['id']) {
            return $this->getMap();
        }

        $site = Website::find($currentMap['id']);
        if($site)
        {
            $site->increment('views_today');
            $site->increment('views_total');
        }

        $user = auth()->user();

        foreach ($input['items'] as $item) {
            if (!isset($currentMap['items'][$item])) {
                continue;
            }

            $item = $currentMap['items'][$item];

            if (!($type = ItemType::where('id', $item['id_real'])->first(['id', 'item_type']))) {
                continue;
            }

            if ($type->item_type == ItemTypes::COIN) {
                $user->increment('coins', $item['count']);
            } else {
                if ($update = $user->items()->where('item_type_id', $item['id_real'])->first(['id'])) {
                    $update->increment('count', $item['count']);
                } else {
                    $itemType->items()->create([
                        'count' => $item['count'],
                        'owner_id' => $user->team ?: $user,
                    ]);
                }
            }
        }

        return $this->getMap();
    }
}
