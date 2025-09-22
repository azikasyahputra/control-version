<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objects extends Model
{
    use HasFactory;

    protected $table = 'objects';

    protected $fillable = [
        'key',
        'value'
    ];

    protected $casts = [
        'value' => 'array',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function getDateFormat(): string
    {
        return 'U';
    }
}
