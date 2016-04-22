<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\PMGroup;
use App\PM;
use App\User;
use DB;

class PMController extends Controller
{
    public function getPMs()
    {
        
    }
    
    public function seenPM(User $user, $id)
    {
        if($user->id < $id) {
            PMGroup::where('user_id', $user->id)->where('user2_id', $id)->update(['user_last_seen' => DB::raw('user2_last_message')]);
        } else if($user->id > $id) {
            PMGroup::where('user_id', $id)->where('user2_id', $user->id)->update(['user2_last_seen' => DB::raw('user_last_message')]);
        }
    }

    public function getPM(User $user, $id)
    {
        if($id == $user->id) return;

        $user_id = ($user->id > $id ? $id : $user->id);
        $user2_id = ($user->id > $id ? $user->id : $id);

        $messages = [];
        
        if($group = PMGroup::where('user_id', $user_id)->where('user2_id', $user2_id)->first(['id'])) {
            $pms = $group->pms()->get(['message', 'created_at', 'sender']);
            
            foreach ($pms as $pm) {
                $messages[] = [
                    'side' => (($user->id > $id) == $pm->sender) ? 'left' : 'right',
                    'message' => $pm->message,
                    'info' => $pm->created_at->format('M j h:i A')
                ];
            }
        }
        
        return [
            'messages' => $messages,
            'gravatar' => md5(User::findOrFail($id, ['email'])->email)
        ];
    }

    public function postPM(Request $request, User $user, $id)
    {
        if($id == $user->id || !$request->input('message')) return;

        $user_id = ($user->id > $id ? $id : $user->id);
        $user2_id = ($user->id > $id ? $user->id : $id);
        $sender = ($id > $user->id);

        if(!($group = PMGroup::where('user_id', $user_id)->where('user2_id', $user2_id)->first(['id']))) {
            $group = new PMGroup;
            $group->user_id = $user_id;
            $group->user2_id = $user2_id;
        }

        if($sender) {
            $group->user_last_seen = $group->user_last_message = Carbon::now();
        } else {
            $group->user2_last_seen = $group->user2_last_message = Carbon::now();
        }
        
        $group->save();

        $message = $group->pms()->create([
            'sender' => $sender,
            'message' => $request->input('message')
        ]);

        $group->pms()->orderBy('id', 'desc')->skip(20)->get()->delete();
    }
}
