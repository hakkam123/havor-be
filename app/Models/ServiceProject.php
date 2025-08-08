<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceProject extends Model
{
    protected $table = 'service_project';
    
    protected $fillable = [
        'service_id',
        'project_id'
    ];

    public $timestamps = false;

    protected $primaryKey = ['service_id', 'project_id'];
    public $incrementing = false;

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
