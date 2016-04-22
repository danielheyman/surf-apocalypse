<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class SiteController extends Controller
{
    public function getSites(User $user)
    {
        return $user->websites()->get(['id', 'name', 'url', 'enabled', 'views_total', 'views_today']);
    }

    public function toggleSite(Request $request, User $user, $id)
    {
        $site = $user->websites()->where('id', $id)->first(['id', 'enabled']);
        
        if($site->enabled == $request->input('enabled')) return;
        
        $site->enabled = $request->input('enabled');
        $site->save();
    }

    public function deleteSite(User $user, $id)
    {
        $user->websites()->where('id', $id)->first()->delete();
    }

    public function addSite(Request $request, User $user)
    {
        $this->validate($request, ['name' => 'required|min:2', 'url' => 'required|url']);

        $site = $user->websites()->create($request->only(['name', 'url']));

        return [
            'id' => $site->id,
            'name' => $site ->name,
            'url' => $site->url,
            'enabled' => true
        ];
    }
}
