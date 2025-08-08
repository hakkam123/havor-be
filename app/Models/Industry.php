<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $fillable = [
        'title'
    ];

    public $timestamps = false;

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
