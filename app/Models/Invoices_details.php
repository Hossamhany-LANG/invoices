<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoices_details extends Model
{
    protected $fillable = [
        'invoice_number',
        'id_invoice',
        'product',
        'section',
        'status',
        'value_status',
        'note',
        'user',
        'payment_date'
    ];
}
