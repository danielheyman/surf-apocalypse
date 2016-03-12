<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Image;

class EquipsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getEquip($id)
    {
        $src = base_path() . '/resources/assets/img/sprites/' . app('EquipType')->idToLocation($id);
        return Image::make($src)->response('png');
    }
    
}
