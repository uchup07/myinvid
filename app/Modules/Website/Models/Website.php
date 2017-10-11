<?php

namespace App\Modules\Website\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{

    public function template(){
        return $this->belongsTo('App\Modules\Template\Models\Template');
    }

    public function galleries() {
        return $this->hasMany('App\Modules\Gallery\Models\Gallery', 'website_id', 'id');
    }

    public function stories() {
        return $this->hasMany('App\Modules\Storyoflove\Models\StoryOfLove', 'website_id', 'id');
    }

    public function bridesmaid() {
        return $this->hasMany('App\Modules\Bridesmaid\Models\Bridesmaid', 'website_id', 'id');
    }

    public function website() {
        return $this->hasOne('App\Modules\Email\Models\EmailFormat', 'id', 'website_id');
    }

}
