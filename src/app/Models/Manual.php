<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manual extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'name',
        'description',
        'is_draft',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
