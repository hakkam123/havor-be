<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'main_description',
        'hero_image_url',
        'content_image_url',
        'content_description'
    ];

    const UPDATED_AT = null;
}
