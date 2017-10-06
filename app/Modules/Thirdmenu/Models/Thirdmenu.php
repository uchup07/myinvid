<?php

namespace App\Modules\Thirdmenu\Models;

use Illuminate\Database\Eloquent\Model;

class Thirdmenu extends Model
{
    ### RELATION SUBMENU ###
    public function submenu(){
        return $this->belongsTo('App\Modules\Submenu\Models\Submenu');
    }
}
