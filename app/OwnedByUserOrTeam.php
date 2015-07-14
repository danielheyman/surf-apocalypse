<?php

namespace App;

trait OwnedByUserOrTeam
{
    public function user()
    {
        if ($this->team_owned) {
            return;
        }

        return User::find($this->owner_id);
    }

    public function team()
    {
        if (!$this->team_owned) {
            return;
        }

        return Team::find($this->owner_id);
    }

    public function setOwnerIdAttribute($model)
    {
        if (is_string($model)) {
            return;
        }

        $this->attributes['owner_id'] = $model->id;
        $this->attributes['team_owned'] = $this->isTeam($model);
    }

    public function scopeOwnedBy($query, $model)
    {
        $query->where('owner_id', $model->id)->where('team_owned', $this->isTeam($model));
    }

    public function isTeam($model)
    {
        return get_class($model) == "App\Team";
    }
}
