<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'features',
        'price',
        'duration',
        'is_featured',
        'icon_url'
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'service_project');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'services_id');
    }
}
