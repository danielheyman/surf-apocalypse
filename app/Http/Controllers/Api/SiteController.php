<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Website;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getSites()
    {
        return Auth::user()->websites()->get(['id', 'name', 'url', 'enabled', 'views_total', 'views_today']);
    }

    public function toggleSite(Request $request, $id)
    {
        $site = Auth::user()->websites()->where('id', $id)->first();
        if($site->enabled == $request->input('enabled'))
            return;

        $site->enabled = $request->input('enabled');
        $site->save();
    }

    public function deleteSite($id)
    {
        Auth::user()->websites()->where('id', $id)->first()->delete();
    }

    public function addSite(Request $request)
    {
        $this->validate($request, ['name' => 'required|min:2', 'url' => 'required|url']);

        $site = Auth::user()->websites()->create($request->all());

        return [
            'id' => $site->id,
            'name' => $site ->name,
            'url' => $site->url,
            'enabled' => true
        ];
    }
}
