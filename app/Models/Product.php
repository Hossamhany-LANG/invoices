<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'description',
        'created_by',
        'section_id'
    ];
    public function section()
    {
    return $this->belongsTo(section::class);
    }
}
