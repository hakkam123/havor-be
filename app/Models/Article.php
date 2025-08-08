<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'short_description',
        'content',
        'image_url',
        'author',
        'industry_id',
        'services_id'
    ];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'services_id');
    }
}
