<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Website;
use Session;

class MapController extends Controller
{
    public function getMap()
    {
        $map_items = [];
        $hash = [
            'id' => '',
            'items' => [],
        ];
        
        $target = User::where('human', true)->where('website_count', '>', 0)->orderByRaw('RANDOM()')->first();
        foreach(auth()->user()->itemManager()->find($target) as $key => $value) {
            $hash = str_random(60);
            $ids['items'][$hash] = ['id' => $key, 'count' => $value];
            $map_items[] = [
                'id' => $hash,
                'type' => $key,
                'count' => $value,
            ];
        }
        
        // TODO: Calculate the amount of damage done to target
        // TODO: Team surfing

        $site = $target->websites()->where('enabled', true)->orderByRaw('RANDOM()')->first(['id', 'url', 'name'])->toArray();

        $ids['id'] = $site['id'];

        $map = array_merge($site, [
            'items' => $map_items,
            'id' => md5($ids['id']),
            'target_info' => [
                "name" => $target->name,
                "equip" => $target->equipManager()->myEquipsToString(),
                "id" => $target->id
            ]
        ]);

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

            $itemManager = auth()->user()->itemManager();
            foreach ($input['items'] as $item) {
                if (isset($currentMap['items'][$item])) {
                    $item = $currentMap['items'][$item];
                    $itemManager->give($item['id'], $item['count']);
                }
            }
            $itemManager->give('views_today');
            $itemManager->give('views_total');
        }

        return $this->getMap();
    }
}
