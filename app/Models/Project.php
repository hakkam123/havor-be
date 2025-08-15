<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'content',
        'image_url',
        'client_id',
        'client_name',
        'service_id',
        'project_date',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }
    
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_project');
    }
}
