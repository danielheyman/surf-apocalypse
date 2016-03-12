<?php

namespace App;

class EquipmentType
{
    function nameToId($name) {
        $name = explode('/', $name);
        $code = "";
        $folder = config('equipment.id');
        for($i = 0; $i < count($name); $i++) {
            if($i + 1 != count($name)) {
                $code .= array_search($name[$i], array_keys($folder)) + 100;
                $folder = $folder[$name[$i]];
            } else {
                $code .= array_search($name[$i], $folder) + 100;
            }
        }
        return $code * 10;
    }
    function createFromName($name, $desc) {
        return ItemType::create([
            'name' => $desc,
            'sprite' => $this->nameToId($name),
            'users_allowed' => UsersAllowed::HUMAN,
            'item_type' => ItemTypes::EQUIP
        ]);
    }
    function nameToItemType($name) {
         return ItemType::where('sprite', $this->nameToId($name))->first();
    }
    function idToLocation($id) {
        $id = strval($id);
        $config = config('equipment.id');
        if(strlen($id) >= 9) {
            $key1 = array_keys($config)[intval(substr($id, 0, 3)) - 100];
            $key2 = array_keys($config[$key1])[intval(substr($id, 3, 3)) - 100];
            $key3 = $config[$key1][$key2][intval(substr($id, 6, 3)) - 100];
            return "{$key1}/{$key2}/{$key3}.png";
        } else {
            $key1 = array_keys($config)[intval(substr($id, 0, 3)) - 100];
            $key2 = $config[$key1][intval(substr($id, 3, 3)) - 100];
            return "{$key1}/{$key2}.png";
        }
    }
    function idToPriority($id) {
        $id = strval($id);
        $config = config('equipment');
        $folder = array_keys($config['id'])[intval(substr($id, 0, 3)) - 100];
        return array_search($folder, $config['priority']);
    }
    
}
