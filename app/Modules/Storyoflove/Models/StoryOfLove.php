<?php

namespace App\Modules\Storyoflove\Models;

use Illuminate\Database\Eloquent\Model;

class StoryOfLove extends Model
{

    public function website(){
        return $this->belongsTo('App\Modules\Website\Models\Website');
    }

}
