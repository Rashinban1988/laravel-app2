<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function manuals()
    {
        return $this->hasMany(Manual::class);
    }
}
