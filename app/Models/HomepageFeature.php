<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageFeature extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon_url',
        'order_index'
    ];

    // Tidak menggunakan timestamps
    public $timestamps = false;
}
