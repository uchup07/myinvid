<?php

namespace App\Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public function website()
    {
        return $this->hasOne('App\Modules\Website\Models\Website','id','website_id');
    }

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function invoice_detail()
    {
        return $this->hasMany('App\Modules\Invoice\Models\InvoiceDetail');
    }

}
