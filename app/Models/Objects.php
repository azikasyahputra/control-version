<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objects extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'objects';

    protected $fillable = [
        'key',
        'value',
        'created_at',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = $value ?: time();
    }
}
