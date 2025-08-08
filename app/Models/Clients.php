<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    protected $fillable = [
        'title',
        'description', 
        'icon_url'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }
}
