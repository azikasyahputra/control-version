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
        'created_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = time();
            }
        });
    }

    public function setValueAttribute($value)
    {
        if(is_array($value)){
            $this->attributes['value'] = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }else{
            $this->attributes['value'] = $value;
        }
    }

    public function getValueAttribute($value)
    {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $value = $decoded;
        }

        if(is_bool($value)){
            $value = $value ? 'true' : 'false';
        }

        return $value;
    }
}
