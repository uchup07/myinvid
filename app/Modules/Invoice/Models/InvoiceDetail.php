<?php

namespace App\Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = 'invoice_details';
    protected $connection;

    public function __construct(array $attributes = [])
    {
        $this->connection = env('DB_CONNECTION', 'mysql');
        parent::__construct($attributes);
    }

}
