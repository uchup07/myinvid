<?php

namespace App\Modules\Confirmation\Models;

use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    public function confirmation_type(){
        return $this->hasOne('App\Modules\Confirmation\Models\ConfirmationType', 'confirmation_type_id', 'id');
    }

    public function status_approve(){
        return $this->hasOne('App\Modules\Confirmation\Models\StatusApprove', 'id', 'approved');
    }

    public function website(){
        return $this->hasOne('App\Modules\Website\Models\Website', 'id', 'website_id');
    }

    public function invoice(){
        return $this->hasOne('App\Modules\Invoice\Models\Invoice', 'id', 'invoice_id');
    }

    public function user(){
        return $this->hasOne('App\User','id','user_id');
    }

}
